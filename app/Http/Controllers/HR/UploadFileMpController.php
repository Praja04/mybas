<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Jobs\HR\ProcessHrMasterEmployeeImport;
use App\Models\HR\HrMasterEmployee;
use App\Models\HR\HrMasterEmployeeBatch;
use App\Models\HR\HrMasterEmployeeStaging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadFileMpController extends Controller
{
    /**
     * Cek permission dengan mempertimbangkan group + direct user permission
     * (Auth::user()->getAllPermissionCodenames() menggabungkan keduanya,
     *  sedangkan $this->permission() bawaan base controller hanya cek group).
     */
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->checkPermission('hr_upload_file_mp');
        $typeKaryawan = $request->get('type_karyawan');
        return view('hr.upload-file-mp.upload-file-mp', [
            'typeKaryawan' => $typeKaryawan,
            'isMitraKerja' => $typeKaryawan === 'mitra_kerja',
        ]);
    }

    public function upload(Request $request)
    {
        $this->checkPermission('hr_upload_file_mp');

        $rules = [
            'file' => 'required|file|mimes:csv,txt',
        ];

        $typeKaryawan      = $request->get('type_karyawan');
        $mitraKerjaChoice  = null;
        if ($typeKaryawan === 'mitra_kerja') {
            $rules['mitra_kerja_choice'] = 'required|in:KMJ,Fortuna';
            $mitraKerjaChoice = $request->get('mitra_kerja_choice');
        }

        $request->validate($rules);

        $file = $request->file('file');
        $batchId = (string) Str::uuid();

        $dir = 'uploads/hr-master-employee';
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($dir, $filename, 'public');

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $batch = HrMasterEmployeeBatch::create([
            'batch_id'         => $batchId,
            'filename'         => $file->getClientOriginalName(),
            'send_by_username' => $username,
            'status'           => 'pending',
            'file_path'        => $path,
        ]);

        ProcessHrMasterEmployeeImport::dispatch(
            $batchId,
            storage_path('app/public/' . $path),
            $username,
            $mitraKerjaChoice
        );

        return response()->json([
            'success'  => true,
            'message'  => 'File diterima, sedang diproses oleh queue worker.',
            'batch_id' => $batchId,
        ]);
    }

    public function history(Request $request)
    {
        $this->checkPermission('hr_upload_file_mp');

        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $username = Auth::user()->username ?? (Auth::user()->name ?? '');

        $query = HrMasterEmployeeBatch::where('send_by_username', $username)
            ->orderBy('id', 'desc');

        $total     = (clone $query)->count();
        $lastPage  = max(1, (int) ceil($total / $perPage));

        $batches = $query->forPage($page, $perPage)->get()->map(function ($b) {
            $unconfirmed = ($b->created_count + $b->updated_count) - $b->confirmed_count;
            return [
                'id'              => $b->id,
                'batch_id'        => $b->batch_id,
                'filename'        => $b->filename,
                'send_by_username'=> $b->send_by_username,
                'created_count'   => (int) $b->created_count,
                'updated_count'   => (int) $b->updated_count,
                'confirmed_count' => (int) $b->confirmed_count,
                'unconfirmed'     => max(0, $unconfirmed),
                'status'          => $b->status,
                'created_at'      => optional($b->created_at)->format('d M Y H:i'),
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
        $this->checkPermission('hr_upload_file_mp');

        $perPage = (int) $request->get('per_page', 50);
        $page    = (int) $request->get('page', 1);

        $baseQuery = HrMasterEmployeeStaging::where('batch_id', $batchId);

        $total     = (clone $baseQuery)->count();
        $created   = (clone $baseQuery)->where('status', 'created')->count();
        $updated   = (clone $baseQuery)->where('status', 'updated')->count();
        $confirmed = (clone $baseQuery)->where('status', 'confirmed')->count();

        $rows = (clone $baseQuery)->orderBy('NIK')->forPage($page, $perPage)->get();

        $batch = HrMasterEmployeeBatch::where('batch_id', $batchId)->first();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'last_page'   => max(1, (int) ceil($total / $perPage)),
                'created'     => $created,
                'updated'     => $updated,
                'confirmed'   => $confirmed,
                'filename'    => $batch ? $batch->filename : '-',
            ],
        ]);
    }

    public function confirm(Request $request, $batchId)
    {
        $this->checkPermission('hr_upload_file_mp');

        $request->validate([
            'niks'   => 'required|array|min:1',
            'niks.*' => 'string',
        ]);

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $staging = HrMasterEmployeeStaging::where('batch_id', $batchId)
            ->whereIn('NIK', $request->niks)
            ->get();

        $confirmed = 0;
        DB::transaction(function () use ($staging, $username, &$confirmed) {
            foreach ($staging as $row) {
                $data = $row->only([
                    'NIK', 'Nama', 'Tgl Lahir', 'Tgl Masuk', 'Departmen', 'Sub Departmen',
                    'Section', 'Tipe Karyawan', 'Jabatan', 'Jenis Kelamin', 'Work Status',
                    'Status Nikah', 'Aktif', 'Valid From',
                ]);
                $data['send_by_username'] = $username;

                HrMasterEmployee::updateOrCreate(['NIK' => $row->NIK], $data);

                $row->status = 'confirmed';
                $row->save();
                $confirmed++;
            }
        });

        $batch = HrMasterEmployeeBatch::where('batch_id', $batchId)->first();
        if ($batch) {
            $batch->confirmed_count = (int) $batch->confirmed_count + $confirmed;
            $batch->save();
        }

        // Invalidate cache HR Dashboard dropdown setelah data berubah
        // (dept/sub-dept/tipe bisa baru / berubah setelah confirm batch)
        Cache::forget('hr.distinct.Departmen');
        Cache::forget('hr.distinct.Sub Departmen');
        Cache::forget('hr.distinct.Tipe Karyawan');

        return response()->json([
            'success'   => true,
            'message'   => "Berhasil konfirmasi {$confirmed} data.",
            'confirmed' => $confirmed,
        ]);
    }

    public function employees(Request $request)
    {
        $this->checkPermission('hr_upload_file_mp');

        $plan    = $request->get('plan', 'PAS');
        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 25);
        $page    = (int) $request->get('page', 1);

        $perPage = max(1, min($perPage, 100));
        $page    = max(1, $page);

        $query = HrMasterEmployee::query()
            ->leftJoin('users', 'users.username', '=', 'hr_master_employee.send_by_username')
            ->select(
                'hr_master_employee.*',
                'users.name as updated_by_name'
            );
        if ($search !== '') {
            $query->where('hr_master_employee.NIK', 'like', "%{$search}%");
        }

        $total = (clone $query)->count();
        $rows  = $query->orderBy('hr_master_employee.NIK')
            ->forPage($page, $perPage)
            ->get();

        return response()->json([
            'data' => $rows,
            'plan' => $plan,
            'meta' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
