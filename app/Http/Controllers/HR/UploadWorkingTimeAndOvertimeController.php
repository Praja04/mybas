<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Jobs\HR\ProcessHrWorkingTimeAndOvertimeConfirm;
use App\Jobs\HR\ProcessHrWorkingTimeAndOvertimeImport;
use App\Models\HR\HrWorkingTimeAndOvertime;
use App\Models\HR\HrWorkingTimeAndOvertimeBatch;
use App\Models\HR\HrWorkingTimeAndOvertimeStaging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadWorkingTimeAndOvertimeController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    public function index()
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');
        return view('hr.upload-working-time-and-overtime.upload-working-time-and-overtime');
    }

    public function upload(Request $request)
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $batchId = (string) Str::uuid();

        $dir = 'uploads/hr-working-time-and-overtime';
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($dir, $filename, 'public');

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $batch = HrWorkingTimeAndOvertimeBatch::create([
            'batch_id'         => $batchId,
            'filename'         => $file->getClientOriginalName(),
            'send_by_username' => $username,
            'status'           => 'pending',
            'file_path'        => $path,
        ]);

        ProcessHrWorkingTimeAndOvertimeImport::dispatch($batchId, storage_path('app/public/' . $path), $username);

        return response()->json([
            'success'  => true,
            'message'  => 'File diterima, sedang diproses oleh queue worker.',
            'batch_id' => $batchId,
        ]);
    }

    public function history(Request $request)
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');

        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $username = Auth::user()->username ?? (Auth::user()->name ?? '');

        $query = HrWorkingTimeAndOvertimeBatch::where('send_by_username', $username)
            ->orderBy('id', 'desc');

        $total    = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        $batches = $query->forPage($page, $perPage)->get()->map(function ($b) {
            $unconfirmed = ($b->created_count + $b->updated_count) - $b->confirmed_count;
            return [
                'id'               => $b->id,
                'batch_id'         => $b->batch_id,
                'filename'         => $b->filename,
                'send_by_username' => $b->send_by_username,
                'created_count'    => (int) $b->created_count,
                'updated_count'    => (int) $b->updated_count,
                'confirmed_count'  => (int) $b->confirmed_count,
                'unconfirmed'      => max(0, $unconfirmed),
                'status'           => $b->status,
                'confirm_status'   => $b->confirm_status,
                'created_at'       => optional($b->created_at)->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'data' => $batches,
            'meta' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    public function review($batchId, Request $request)
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');

        $perPage = (int) $request->get('per_page', 50);
        $page    = (int) $request->get('page', 1);

        $baseQuery = HrWorkingTimeAndOvertimeStaging::where('batch_id', $batchId);

        $total   = (clone $baseQuery)->count();
        $created = (clone $baseQuery)->where('status', 'created')->count();
        $updated = (clone $baseQuery)->where('status', 'updated')->count();
        $batch   = HrWorkingTimeAndOvertimeBatch::where('batch_id', $batchId)->first();

        $rows = (clone $baseQuery)->orderBy('id')->forPage($page, $perPage)->get();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
                'created'   => $created,
                'updated'   => $updated,
                'confirmed' => $batch ? (int) $batch->confirmed_count : 0,
                'filename'  => $batch ? $batch->filename : '-',
            ],
        ]);
    }

    public function confirm(Request $request, $batchId)
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');

        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $batch = HrWorkingTimeAndOvertimeBatch::where('batch_id', $batchId)->first();
        if (! $batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        if (in_array($batch->confirm_status, ['processing', 'pending'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Konfirmasi sebelumnya masih berjalan, mohon tunggu.',
            ], 409);
        }

        $validIds = HrWorkingTimeAndOvertimeStaging::where('batch_id', $batchId)
            ->whereIn('id', $request->ids)
            ->pluck('id')
            ->all();

        if (empty($validIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid untuk dikonfirmasi (mungkin sudah diproses).',
            ], 422);
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $batch->update([
            'confirm_status'    => 'pending',
            'confirm_total'     => count($validIds),
            'confirm_processed' => 0,
            'confirm_error'     => null,
        ]);

        ProcessHrWorkingTimeAndOvertimeConfirm::dispatch($batchId, $validIds, $username);

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi sedang diproses oleh queue worker. Silakan tunggu notifikasi.',
        ]);
    }

    public function confirmStatus($batchId)
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');

        $batch = HrWorkingTimeAndOvertimeBatch::where('batch_id', $batchId)->first();
        if (! $batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        return response()->json([
            'success'           => true,
            'confirm_status'    => $batch->confirm_status,
            'confirm_total'     => (int) $batch->confirm_total,
            'confirm_processed' => (int) $batch->confirm_processed,
            'confirmed_count'   => (int) $batch->confirmed_count,
            'confirm_error'     => $batch->confirm_error,
        ]);
    }

    public function records(Request $request)
    {
        $this->checkPermission('hr_upload_working_time_and_overtime');

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 25);
        $page    = (int) $request->get('page', 1);

        $perPage = max(1, min($perPage, 100));
        $page    = max(1, $page);

        $query = HrWorkingTimeAndOvertime::query()
            ->leftJoin('users', 'users.username', '=', 'hr_workingtimeandovertime.send_by_username')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'hr_workingtimeandovertime.nik')
            ->select(
                'hr_workingtimeandovertime.*',
                'users.name as updated_by_name',
                DB::raw('hme.`Sub Departmen` as sub_departmen')
            );

        if ($search !== '') {
            $query->where('hr_workingtimeandovertime.nik', 'like', "%{$search}%");
        }

        $total = (clone $query)->count();
        $rows  = $query->orderBy('hr_workingtimeandovertime.id', 'desc')
            ->forPage($page, $perPage)
            ->get();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
