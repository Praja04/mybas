<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Jobs\HR\ProcessHrIzinConfirm;
use App\Jobs\HR\ProcessHrIzinImport;
use App\Models\HR\HrIzin;
use App\Models\HR\HrIzinBatch;
use App\Models\HR\HrIzinStaging;
use App\Models\HR\HrWorkingTimeAndOvertime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadFileMangkirHrdashController extends Controller
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
        $this->checkPermission('hr_upload_file_mangkir_hrdash');
        return view('hr.upload-file-mangkir-hrdash.upload-file-mangkir-hrdash');
    }

    public function upload(Request $request)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');

        $tmpPath = $file->getRealPath();
        $fh = fopen($tmpPath, 'r');
        if ($fh !== false) {
            $firstLine = fgets($fh);
            fclose($fh);
            if ($firstLine !== false && mb_stripos($firstLine, 'Kode Ijin') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'File yang diupload adalah template Izin (mengandung kolom "Kode Ijin"), bukan template Mangkir. Silakan upload file mangkir yang benar.',
                ], 422);
            }
        }

        $batchId = (string) Str::uuid();

        $dir = 'uploads/hr-mangkir-hrdash';
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($dir, $filename, 'public');

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $batch = HrIzinBatch::create([
            'batch_id'         => $batchId,
            'filename'         => $file->getClientOriginalName(),
            'send_by_username' => $username,
            'status'           => 'pending',
            'file_path'        => $path,
        ]);

        ProcessHrIzinImport::dispatch($batchId, storage_path('app/public/' . $path), $username, 'mangkir');

        $batch->update(['filename' => '[MANGKIR] ' . $file->getClientOriginalName()]);

        return response()->json([
            'success'  => true,
            'message'  => 'File mangkir diterima, sedang diproses oleh queue worker.',
            'batch_id' => $batchId,
            'filename' => $file->getClientOriginalName(),
        ]);
    }

    public function history(Request $request)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $username = Auth::user()->username ?? (Auth::user()->name ?? '');

        $query = HrIzinBatch::where('send_by_username', $username)
            ->where('filename', 'like', '[MANGKIR]%')
            ->orderBy('id', 'desc');

        $total    = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        $batches = $query->forPage($page, $perPage)->get()->map(function ($b) {
            $unconfirmed = ($b->created_count + $b->updated_count) - $b->confirmed_count;
            return [
                'id'                     => $b->id,
                'batch_id'               => $b->batch_id,
                'filename'               => preg_replace('/^\[MANGKIR\]\s*/', '', $b->filename),
                'send_by_username'       => $b->send_by_username,
                'created_count'          => (int) $b->created_count,
                'updated_count'          => (int) $b->updated_count,
                'confirmed_count'        => (int) $b->confirmed_count,
                'unconfirmed'            => max(0, $unconfirmed),
                'overlap_count'          => (int) $b->overlap_count,
                'deleted_overtime_count' => (int) $b->deleted_overtime_count,
                'deleted_orphan_count'   => (int) ($b->deleted_orphan_count ?? 0),
                'deleted_mangkir_count'  => $this->countMangkirLog($b->overlap_log),
                'status'                 => $b->status,
                'confirm_status'         => $b->confirm_status,
                'created_at'             => optional($b->created_at)->format('d M Y H:i'),
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
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $perPage = (int) $request->get('per_page', 50);
        $page    = (int) $request->get('page', 1);

        $baseQuery = HrIzinStaging::where('batch_id', $batchId)
            ->where('kode_ijin', 'A');

        $total   = (clone $baseQuery)->count();
        $created = (clone $baseQuery)->where('status', 'created')->count();
        $updated = (clone $baseQuery)->where('status', 'updated')->count();
        $batch   = HrIzinBatch::where('batch_id', $batchId)->first();

        $rows = (clone $baseQuery)->orderBy('id')->forPage($page, $perPage)->get();

        $overlapRows = $this->detectOverlaps($batchId);

        return response()->json([
            'data' => $rows,
            'meta' => [
                'page'           => $page,
                'per_page'       => $perPage,
                'total'          => $total,
                'last_page'      => max(1, (int) ceil($total / $perPage)),
                'created'        => $created,
                'updated'        => $updated,
                'confirmed'      => $batch ? (int) $batch->confirmed_count : 0,
                'overlap_count'  => count($overlapRows),
                'filename'       => $batch ? preg_replace('/^\[MANGKIR\]\s*/', '', $batch->filename) : '-',
            ],
            'overlap_rows' => $overlapRows,
        ]);
    }

    public function confirm(Request $request, $batchId)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $batch = HrIzinBatch::where('batch_id', $batchId)->first();
        if (! $batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        if (in_array($batch->confirm_status, ['processing', 'pending'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Konfirmasi sebelumnya masih berjalan, mohon tunggu.',
            ], 409);
        }

        $validIds = HrIzinStaging::where('batch_id', $batchId)
            ->where('kode_ijin', 'A')
            ->whereIn('id', $request->ids)
            ->pluck('id')
            ->all();

        if (empty($validIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid untuk dikonfirmasi (mungkin sudah diproses).',
            ], 422);
        }

        $decisionsRaw = $request->get('decisions', []);
        $decisions = [];
        if (is_array($decisionsRaw)) {
            foreach ($decisionsRaw as $stagingId => $decision) {
                $decisions[(int) $stagingId] = $decision;
            }
        }

        $username = Auth::user()->username ?? (Auth::user()->name ?? 'system');

        $batch->update([
            'confirm_status'    => 'pending',
            'confirm_total'     => count($validIds),
            'confirm_processed' => 0,
            'confirm_error'     => null,
            'overlap_log'       => null,
        ]);

        ProcessHrIzinConfirm::dispatch($batchId, $validIds, $username, $decisions);

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi mangkir sedang diproses oleh queue worker. Silakan tunggu notifikasi.',
        ]);
    }

    public function confirmStatus($batchId)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $batch = HrIzinBatch::where('batch_id', $batchId)->first();
        if (! $batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        return response()->json([
            'success'                  => true,
            'confirm_status'           => $batch->confirm_status,
            'confirm_total'            => (int) $batch->confirm_total,
            'confirm_processed'        => (int) $batch->confirm_processed,
            'confirmed_count'          => (int) $batch->confirmed_count,
            'overlap_count'            => (int) $batch->overlap_count,
            'deleted_overtime_count'   => (int) $batch->deleted_overtime_count,
            'deleted_mangkir_count'    => $this->countMangkirLog($batch->overlap_log),
            'overlap_log'              => $batch->overlap_log,
            'confirm_error'            => $batch->confirm_error,
        ]);
    }

    public function records(Request $request)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 25);
        $page    = (int) $request->get('page', 1);

        $perPage = max(1, min($perPage, 100));
        $page    = max(1, $page);

        $query = HrIzin::query()
            ->leftJoin('users', 'users.username', '=', 'hr_izin.send_by_username')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'hr_izin.nik')
            ->where('hr_izin.kode_ijin', 'A')
            ->select(
                'hr_izin.*',
                'users.name as updated_by_name',
                DB::raw('hme.`Sub Departmen` as sub_departmen')
            );

        if ($search !== '') {
            $query->where('hr_izin.nik', 'like', "%{$search}%");
        }

        $total = (clone $query)->count();
        $rows  = $query->orderBy('hr_izin.id', 'desc')
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

    public function checkOrphans(Request $request, $batchId)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $request->validate([
            'tgl_from' => 'required|date_format:Y-m-d',
            'tgl_to'   => 'required|date_format:Y-m-d|after_or_equal:tgl_from',
            'tipe'     => 'required|in:Staff,Non Staff',
        ]);

        $batch = HrIzinBatch::where('batch_id', $batchId)->first();
        if (! $batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        $tglFrom = $request->get('tgl_from');
        $tglTo   = $request->get('tgl_to');
        $tipe    = $request->get('tipe');

        $existing = HrIzin::query()
            ->join('hr_master_employee as hme', 'hme.NIK', '=', 'hr_izin.nik')
            ->where('hr_izin.kode_ijin', 'A')
            ->where(DB::raw('hme.`Tipe Karyawan`'), $tipe)
            ->whereBetween('hr_izin.tgl', [$tglFrom, $tglTo])
            ->get(['hr_izin.id', 'hr_izin.nik', 'hr_izin.nama', 'hr_izin.dept', 'hr_izin.section', 'hr_izin.tgl']);

        $newRows = HrIzinStaging::query()
            ->join('hr_master_employee as hme', 'hme.NIK', '=', 'hr_izin_staging.nik')
            ->where('hr_izin_staging.batch_id', $batchId)
            ->where('hr_izin_staging.kode_ijin', 'A')
            ->where(DB::raw('hme.`Tipe Karyawan`'), $tipe)
            ->whereNotNull('hr_izin_staging.tgl')
            ->whereNotNull('hr_izin_staging.nik')
            ->get(['hr_izin_staging.nik', 'hr_izin_staging.tgl']);

        $newKeys = [];
        foreach ($newRows as $nr) {
            $newKeys[$nr->nik . '|' . $nr->tgl] = true;
        }

        $missing = [];
        foreach ($existing as $ex) {
            $key = $ex->nik . '|' . $ex->tgl;
            if (isset($newKeys[$key])) {
                continue;
            }
            $missing[] = [
                'id'      => (int) $ex->id,
                'nik'     => $ex->nik,
                'nama'    => $ex->nama,
                'dept'    => $ex->dept,
                'section' => $ex->section,
                'tgl'     => $ex->tgl,
            ];
        }

        return response()->json([
            'success'         => true,
            'tgl_from'        => $tglFrom,
            'tgl_to'          => $tglTo,
            'tipe'            => $tipe,
            'existing_total'  => $existing->count(),
            'new_total'       => $newRows->count(),
            'missing_count'   => count($missing),
            'missing'         => $missing,
        ]);
    }

    public function deleteOrphans(Request $request, $batchId)
    {
        $this->checkPermission('hr_upload_file_mangkir_hrdash');

        $request->validate([
            'orphans'           => 'required|array|min:1',
            'orphans.*.nik'     => 'required|string',
            'orphans.*.tgl'     => 'required|date_format:Y-m-d',
        ]);

        $batch = HrIzinBatch::where('batch_id', $batchId)->first();
        if (! $batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        $orphans  = $request->get('orphans');
        $deleted  = 0;
        $notFound = 0;

        foreach ($orphans as $o) {
            $count = HrIzin::where('kode_ijin', 'A')
                ->where('nik', $o['nik'])
                ->where('tgl', $o['tgl'])
                ->delete();
            $deleted += $count;
            if ($count === 0) {
                $notFound++;
            }
        }

        if ($deleted > 0) {
            $batch->increment('deleted_orphan_count', $deleted);
        }

        return response()->json([
            'success'      => true,
            'deleted'      => $deleted,
            'not_found'    => $notFound,
            'message'      => $deleted > 0
                ? "Berhasil menghapus {$deleted} data orphan."
                : 'Tidak ada data yang dihapus (mungkin sudah tidak ada di database).',
        ]);
    }

    protected function detectOverlaps(string $batchId): array
    {
        $rows = HrIzinStaging::where('batch_id', $batchId)
            ->where('kode_ijin', 'A')
            ->whereNotNull('tgl')
            ->whereNotNull('nik')
            ->get(['id', 'nik', 'nama', 'dept', 'section', 'tgl', 'no_spi', 'kode_ijin', 'keterangan', 'status']);

        if ($rows->isEmpty()) {
            return [];
        }

        $niks = $rows->pluck('nik')->unique()->all();
        $tgls = $rows->pluck('tgl')->unique()->all();

        $overtime = HrWorkingTimeAndOvertime::whereIn('nik', $niks)
            ->whereIn('tgl_in', $tgls)
            ->get(['nik', 'tgl_in', 'no_spkl', 'jam_spkl', 'jam_hovt']);

        if ($overtime->isEmpty()) {
            return [];
        }

        $overtimeIndex = [];
        foreach ($overtime as $o) {
            $key = $o->nik . '|' . ($o->tgl_in ?? '');
            $overtimeIndex[$key][] = $o;
        }

        $overlapRows = [];
        foreach ($rows as $r) {
            $key = $r->nik . '|' . ($r->tgl ?? '');
            if (!isset($overtimeIndex[$key])) {
                continue;
            }
            foreach ($overtimeIndex[$key] as $o) {
                $overlapRows[] = [
                    'staging_id'   => (int) $r->id,
                    'nik'          => $r->nik,
                    'nama'         => $r->nama,
                    'dept'         => $r->dept,
                    'section'      => $r->section,
                    'tgl'          => $r->tgl,
                    'no_spi'       => $r->no_spi,
                    'kode_ijin'    => $r->kode_ijin,
                    'keterangan'   => $r->keterangan,
                    'status'       => $r->status,
                    'overtime'     => [
                        'no_spkl'  => $o->no_spkl,
                        'jam_spkl' => $o->jam_spkl,
                        'jam_hovt' => $o->jam_hovt,
                    ],
                ];
            }
        }

        return $overlapRows;
    }

    private function countMangkirLog(?string $overlapLog): int
    {
        if (empty($overlapLog)) {
            return 0;
        }
        $lines = array_filter(explode("\n", $overlapLog));
        $count = 0;
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '[MANGKIR]')) {
                $count++;
            }
        }
        return $count;
    }
}
