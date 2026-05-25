<?php
namespace App\Http\Controllers\HRConnect;

use App\Exports\HRConnect\KaryawanAktifExport;
use App\HrGoodieApd;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Models\Loker\Penghuni;
use App\Models\Loker\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class GAShiftInController extends Controller
{
    public function getData(Request $req)
    {
        $query = HrKaryawan::with(['penghuni' => function ($q) {
            $q->where('is_active', 'Y');
        }])
        // ->where('in_complete', 'N')
            ->where('tanggal_masuk', '>', '2025-01-01')
            ->orderBy('tanggal_masuk', 'asc');

        if ($req->tampilkan_semua == 0) {
            $query->where('tanggal_masuk', $req->tanggal);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addColumn('checkStaff', function ($row) {
                $dataLoker = DB::table('loker_penghuni')
                    ->where('nik', $row->nik)
                    ->first();

                if ($dataLoker) {
                    if (strtolower($dataLoker->kategori_karyawan) == 'staff') {
                        return 'Y';
                    } else {
                        return 'N';
                    }
                }

                return $row->staff;
            })
            ->make(true);
    }

    public function index()
    {
        $title = 'GA - Karyawan Masuk';

        $tanggalTersedia = HrKaryawan::where('in_complete', 'N')
            ->where('tanggal_masuk', '>', '2025-01-01')
            ->select('tanggal_masuk')
            ->distinct()
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk');

        $allLokersPria   = Rak::where('kode_rak', 'LP')->where('is_active', 'Y')->get();
        $allLokersWanita = Rak::where('kode_rak', 'LW')->where('is_active', 'Y')->get();

        $penghuniPria   = Penghuni::where('kode_rak', 'LP')->where('is_active', 'Y')->get()->groupBy('no_loker');
        $penghuniWanita = Penghuni::where('kode_rak', 'LW')->where('is_active', 'Y')->get()->groupBy('no_loker');

        // Filter Loker Pria (LP)
        $lokerPria = $allLokersPria->map(function ($rak) use ($penghuniPria) {
            $penghuni = $penghuniPria->get($rak->no_loker) ?? collect();

            $rak->total_penghuni    = $penghuni->count();
            $rak->kategori_tersedia = $penghuni->first() ? strtolower(trim($penghuni->first()->kategori_karyawan)) : null;

            return $rak;
        })->filter(function ($rak) {
            if ($rak->kategori_tersedia == 'staff' && $rak->total_penghuni >= 1) {
                return false;
            }

            if ($rak->kategori_tersedia == 'mitra_kerja' || $rak->kategori_tersedia == 'mitra') {
                return false;
            }

            if ($rak->total_penghuni >= $rak->kapasitas) {
                return false;
            }

            return true;
        })->values();

        // Filter Loker Wanita (LW)
        $lokerWanita = $allLokersWanita->map(function ($rak) use ($penghuniWanita) {
            $penghuni = $penghuniWanita->get($rak->no_loker) ?? collect();

            $rak->total_penghuni    = $penghuni->count();
            $rak->kategori_tersedia = $penghuni->first() ? strtolower(trim($penghuni->first()->kategori_karyawan)) : null;

            return $rak;
        })->filter(function ($rak) {
            if ($rak->kategori_tersedia == 'staff' && $rak->total_penghuni >= 1) {
                return false;
            }

            if ($rak->kategori_tersedia == 'mitra_kerja' || $rak->kategori_tersedia == 'mitra') {
                return false;
            }

            if ($rak->total_penghuni >= $rak->kapasitas) {
                return false;
            }

            return true;
        })->values();

        return view('hr-connect.ga.shift-in', compact('title', 'lokerPria', 'lokerWanita', 'tanggalTersedia'));
    }

    public function updateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->input('data');

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dikirim.',
                ], 400);
            }

            $tglMasuk = now()->format('Y-m-d');
            $goodie   = HrGoodieApd::firstOrCreate(
                ['tgl_masuk' => $tglMasuk],
                ['jumlah_orang' => 0]
            );

            $goodie->increment('jumlah_orang', count($data));

            foreach ($data as $item) {
                $nik     = $item['nik'];
                $nama    = $item['nama'];
                $divisi  = $item['divisi'];
                $kodeRak = $item['kodeRak'] ?? null;
                $noLoker = $item['noLoker'] ?? null;
                $staff   = $item['staff'];
                $idCard  = $item['idCard'];

                if (! empty($kodeRak) && ! empty($noLoker)) {
                    $hasActive = DB::table('loker_penghuni')
                        ->where('nik', $nik)
                        ->where('is_active', 'Y')
                        ->lockForUpdate()
                        ->exists();

                    if ($hasActive) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "NIK {$nik} - {$nama} sudah memiliki loker aktif.",
                        ], 422);
                    }

                    $rows = DB::table('loker_penghuni')
                        ->select('kategori_karyawan', DB::raw('COUNT(DISTINCT nik) as cnt'))
                        ->where('kode_rak', $kodeRak)
                        ->where('no_loker', $noLoker)
                        ->where('is_active', 'Y')
                        ->groupBy('kategori_karyawan')
                        ->lockForUpdate()
                        ->get();

                    if ($rows->count() > 1) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Loker {$kodeRak}-{$noLoker} sudah terisi kategori lain.",
                        ], 422);
                    }

                    $existingType  = $rows->first()->kategori_karyawan ?? null;
                    $existingCount = (int) ($rows->first()->cnt ?? 0);

                    if ($existingType !== null && $existingType !== $staff) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Loker {$kodeRak}-{$noLoker} sudah terisi kategori lain.",
                        ], 422);
                    }

                    $maxCapacity = ($staff == 'staff') ? 1 : 2;
                    if ($existingCount >= $maxCapacity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Loker {$kodeRak}-{$noLoker} sudah penuh.",
                        ], 422);
                    }

                    DB::table('loker_penghuni')->insert([
                        'nik'               => $nik,
                        'nama'              => $nama,
                        'divisi'            => $divisi,
                        'kode_rak'          => $kodeRak,
                        'no_loker'          => $noLoker,
                        'kategori_karyawan' => $staff,
                        'is_active'         => 'Y',
                        'tgl_masuk'         => now()->format('Y-m-d'),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    DB::table('loker_transaksi')->insert([
                        'nik'            => $nik,
                        'nama'           => $nama,
                        'kode_rak'       => $kodeRak,
                        'no_loker'       => $noLoker,
                        'tipe_transaksi' => 'MASUK',
                        'operator'       => auth()->user()->name ?? 'Sistem GA',
                        'keterangan'     => 'Karyawan Baru via HR Connect',
                        'created_at'     => now(),
                    ]);
                } else {
                    DB::table('loker_transaksi')->insert([
                        'nik'            => $nik,
                        'nama'           => $nama,
                        'kode_rak'       => 'Tidak Memiliki Loker',
                        'no_loker'       => 'Tidak Memiliki Loker',
                        'tipe_transaksi' => 'MASUK',
                        'operator'       => auth()->user()->name ?? 'Sistem GA',
                        'keterangan'     => 'Karyawan Baru via HR Connect',
                        'created_at'     => now(),
                    ]);
                }

                DB::table('hr_karyawan')->where('id', $idCard)->update([
                    'in_complete' => 'Y',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Update Status Error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memproses data.'], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $byDate  = $request->input('tanggal');
        $showAll = $request->input('tampilkan_semua');

        return Excel::download(
            new KaryawanAktifExport($byDate, $showAll),
            'Data Karyawan Baru - ' . ($byDate != null ? 'Per Tanggal ' . $byDate : 'Data Keseluruhan') . '.xlsx'
        );
    }
}
