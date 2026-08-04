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

class UploadFileIzinHrdashController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->checkPermission('hr_upload_file_izin_hrdash');
        $typeKaryawan = $request->get('type_karyawan');
        return view('hr.upload-file-izin-hrdash.upload-file-izin-hrdash', [
            'typeKaryawan' => $typeKaryawan,
            'isMitraKerja' => $typeKaryawan === 'mitra_kerja',
        ]);
    }

    public function upload(Request $request)
    {
        $this->checkPermission('hr_upload_file_izin_hrdash');

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $typeKaryawan = $request->get('type_karyawan');
        $isMitraKerja = $typeKaryawan === 'mitra_kerja';

        $file = $request->file('file');

        $tmpPath = $file->getRealPath();
        $fh = fopen($tmpPath, 'r');
        if ($fh !== false) {
            // Template mitra kerja: baris 1 = judul, baris 2 = header.
            // Template biasa: baris 1 = header.
            if ($isMitraKerja) {
                fgets($fh);
                $headerLine = fgets($fh);
            } else {
                $headerLine = fgets($fh);
            }
            fclose($fh);
            if ($headerLine !== false && mb_stripos($headerLine, 'Kode Ijin') === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'File yang diupload bukan template Izin (tidak mengandung kolom "Kode Ijin"). Silakan upload file izin yang benar.',
                ], 422);
            }
        }

        $batchId = (string) Str::uuid();

        $dir = 'uploads/hr-izin-hrdash';
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

        ProcessHrIzinImport::dispatch(
            $batchId,
            storage_path('app/public/' . $path),
            $username,
            $isMitraKerja ? 'mitra_kerja' : 'izin'
        );

        return response()->json([
            'success'  => true,
            'message'  => 'File diterima, sedang diproses oleh queue worker.',
            'batch_id' => $batchId,
        ]);
    }

    public function history(Request $request)
    {
        $this->checkPermission('hr_upload_file_izin_hrdash');

        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $username = Auth::user()->username ?? (Auth::user()->name ?? '');

        $query = HrIzinBatch::where('send_by_username', $username)
            ->orderBy('id', 'desc');

        $total    = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        $batches = $query->forPage($page, $perPage)->get()->map(function ($b) {
            $unconfirmed = ($b->created_count + $b->updated_count) - $b->confirmed_count;
            return [
                'id'                     => $b->id,
                'batch_id'               => $b->batch_id,
                'filename'               => $b->filename,
                'send_by_username'       => $b->send_by_username,
                'created_count'          => (int) $b->created_count,
                'updated_count'          => (int) $b->updated_count,
                'confirmed_count'        => (int) $b->confirmed_count,
                'unconfirmed'            => max(0, $unconfirmed),
                'overlap_count'          => (int) $b->overlap_count,
                'deleted_overtime_count' => (int) $b->deleted_overtime_count,
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
        $this->checkPermission('hr_upload_file_izin_hrdash');

        $perPage = (int) $request->get('per_page', 50);
        $page    = (int) $request->get('page', 1);

        $baseQuery = HrIzinStaging::where('batch_id', $batchId);

        $total   = (clone $baseQuery)->count();
        $created = (clone $baseQuery)->where('status', 'created')->count();
        $updated = (clone $baseQuery)->where('status', 'updated')->count();
        $batch   = HrIzinBatch::where('batch_id', $batchId)->first();

        $rows = (clone $baseQuery)->orderBy('id')->forPage($page, $perPage)->get();

        $overlapRows = $this->detectOverlaps($batchId);
        $mangkirOverlapRows = $this->detectMangkirOverlaps($batchId);

        return response()->json([
            'data' => $rows,
            'meta' => [
                'page'                  => $page,
                'per_page'              => $perPage,
                'total'                 => $total,
                'last_page'             => max(1, (int) ceil($total / $perPage)),
                'created'               => $created,
                'updated'               => $updated,
                'confirmed'             => $batch ? (int) $batch->confirmed_count : 0,
                'overlap_count'         => count($overlapRows),
                'mangkir_overlap_count' => count($mangkirOverlapRows),
                'filename'              => $batch ? $batch->filename : '-',
            ],
            'overlap_rows'         => $overlapRows,
            'mangkir_overlap_rows' => $mangkirOverlapRows,
            'overlap_log'          => $batch ? $batch->overlap_log : null,
        ]);
    }

    public function confirm(Request $request, $batchId)
    {
        $this->checkPermission('hr_upload_file_izin_hrdash');

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
            'message' => 'Konfirmasi sedang diproses oleh queue worker. Silakan tunggu notifikasi.',
        ]);
    }

    public function confirmStatus($batchId)
    {
        $this->checkPermission('hr_upload_file_izin_hrdash');

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

    public function records(Request $request)
    {
        $this->checkPermission('hr_upload_file_izin_hrdash');

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 25);
        $page    = (int) $request->get('page', 1);

        $perPage = max(1, min($perPage, 100));
        $page    = max(1, $page);

        $query = HrIzin::query()
            ->leftJoin('users', 'users.username', '=', 'hr_izin.send_by_username')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'hr_izin.nik')
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

    protected function detectMangkirOverlaps(string $batchId): array
    {
        $rows = HrIzinStaging::where('batch_id', $batchId)
            ->where('kode_ijin', '!=', 'A')
            ->whereNotNull('tgl')
            ->whereNotNull('nik')
            ->get(['id', 'nik', 'nama', 'dept', 'section', 'tgl', 'no_spi', 'kode_ijin', 'keterangan', 'status']);

        if ($rows->isEmpty()) {
            return [];
        }

        $niks = $rows->pluck('nik')->unique()->all();
        $tgls = $rows->pluck('tgl')->unique()->all();

        $mangkir = HrIzin::whereIn('nik', $niks)
            ->whereIn('tgl', $tgls)
            ->where('kode_ijin', 'A')
            ->get(['id', 'nik', 'nama', 'tgl', 'no_spi', 'kode_ijin']);

        if ($mangkir->isEmpty()) {
            return [];
        }

        $mangkirIndex = [];
        foreach ($mangkir as $m) {
            $key = $m->nik . '|' . ($m->tgl ?? '');
            $mangkirIndex[$key][] = $m;
        }

        $overlapRows = [];
        foreach ($rows as $r) {
            $key = $r->nik . '|' . ($r->tgl ?? '');
            if (!isset($mangkirIndex[$key])) {
                continue;
            }
            foreach ($mangkirIndex[$key] as $m) {
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
                    'mangkir'      => [
                        'id'      => (int) $m->id,
                        'no_spi'  => $m->no_spi,
                    ],
                ];
            }
        }

        return $overlapRows;
    }

    protected function detectOverlaps(string $batchId): array
    {
        $rows = HrIzinStaging::where('batch_id', $batchId)
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
}
