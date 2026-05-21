<?php
namespace App\Http\Controllers\HRConnect;

use App\Exports\HRConnect\KaryawanAktifExport;
use App\Exports\HRConnect\KaryawanBaruExport;
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
        ->addColumn('checkStaff', function($row) {
            $dataLoker = DB::table('loker_penghuni')
            ->where('nik', $row->nik)
            ->first();

            if ($dataLoker) {
                if (strtolower($dataLoker->kategori_karyawan)  == 'staff') {
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

//     public function updateStatus(Request $request)
    //     {
    //         DB::beginTransaction();

//         try {
    //             $data = $request->input('data');

//             if (empty($data)) {
    //                 return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikirim.'], 400);
    //             }

//             /* =====================================================
    //              * MANAGE HR GOODIE APD (Sesuai Struktur)
    //              * ===================================================== */
    //             $tglMasuk = now()->format('Y-m-d');

//             // firstOrCreate memastikan kalau belum ada record di tanggal ini, dia bikin baru.
    //             // Kalau udah ada, dia tarik data yang lama, terus ditambah jumlahnya via increment().
    //             $goodie = HrGoodieApd::firstOrCreate(
    //                 ['tgl_masuk' => $tglMasuk],
    //                 ['jumlah_orang' => 0]
    //             );
    //             $goodie->increment('jumlah_orang', count($data));

//             foreach ($data as $item) {
    //                 $nik     = $item['nik'];
    //                 $nama    = $item['nama'];
    //                 $divisi  = $item['divisi'];
    //                 $kodeRak = $item['kodeRak']; // Akan selalu 'LP' atau 'LW'
    //                 $noLoker = (int) $item['noLoker'];
    //                 $staff   = $item['staff'];  // Isinya: 'staff' atau 'non_staff'
    //                 $idCard  = $item['idCard']; // Ini adalah ID dari tabel hr_karyawan

//                 /* =====================================================
    //                  * 1️⃣ Cegah user punya lebih dari 1 loker aktif
    //                  * ===================================================== */
    //                 $hasActive = DB::table('loker_penghuni')
    //                     ->where('nik', $nik)
    //                     ->where('is_active', 'Y')
    //                     ->lockForUpdate()
    //                     ->exists();

//                 if ($hasActive) {
    //                     DB::rollBack();
    //                     return response()->json(['success' => false, 'message' => "NIK {$nik} sudah memiliki loker aktif."], 422);
    //                 }

//                 /* =====================================================
    //                  * 2️⃣ Cek isi loker saat ini (Sesuai Struktur loker_penghuni)
    //                  * ===================================================== */
    //                 $rows = DB::table('loker_penghuni')
    //                     ->select('kategori_karyawan', DB::raw('COUNT(DISTINCT nik) as cnt'))
    //                     ->where('kode_rak', $kodeRak)
    //                     ->where('no_loker', $noLoker)
    //                     ->where('is_active', 'Y')
    //                     ->groupBy('kategori_karyawan')
    //                     ->lockForUpdate()
    //                     ->get();

//                 if ($rows->count() > 1) {
    //                     DB::rollBack();
    //                     return response()->json(['success' => false, 'message' => "Loker {$kodeRak}-{$noLoker} tidak valid karena terdapat campuran kategori eksisting."], 409);
    //                 }

//                 $existingType  = $rows->first()->kategori_karyawan ?? null;
    //                 $existingCount = (int) ($rows->first()->cnt ?? 0);

//                 if ($existingType !== null && $existingType !== $staff) {
    //                     DB::rollBack();
    //                     return response()->json(['success' => false, 'message' => "Loker {$kodeRak}-{$noLoker} sudah dipakai oleh kategori lain."], 422);
    //                 }

//                 /* =====================================================
    //                  * 3️⃣ Validasi kapasitas
    //                  * ===================================================== */
    //                 $maxCapacity = ($staff === 'staff') ? 1 : 2;

//                 if ($existingCount >= $maxCapacity) {
    //                     DB::rollBack();
    //                     return response()->json(['success' => false, 'message' => "Loker {$kodeRak}-{$noLoker} sudah penuh."], 422);
    //                 }

//                 /* =====================================================
    //                  * 4️⃣ Insert Rak Loker Penghuni
    //                  * ===================================================== */
    //                 DB::table('loker_penghuni')->insert([
    //                     'nik'               => $nik,
    //                     'nama'              => $nama,
    //                     'divisi'            => $divisi,
    //                     'kode_rak'          => $kodeRak,
    //                     'no_loker'          => $noLoker,
    //                     'kategori_karyawan' => $staff,
    //                     'is_active'         => 'Y',
    //                     'tgl_masuk'         => now()->format('Y-m-d'),
    //                     'created_at'        => now(),
    //                     'updated_at'        => now(),
    //                 ]);

// /* =====================================================
    //                  * 5️⃣ Catat Histori ke loker_transaksi
    //                  * ===================================================== */
    //                 DB::table('loker_transaksi')->insert([
    //                     'nik'            => $nik,
    //                     'nama'           => $nama,
    //                     'kode_rak'       => $kodeRak,
    //                     'no_loker'       => $noLoker,
    //                     'tipe_transaksi' => 'MASUK', // Sesuai enum lu
    //                     'operator'       => auth()->user()->name ?? 'System',
    //                     'keterangan'     => 'Karyawan Baru Join via HR Connect',
    //                     'created_at'     => now(),
    //                 ]);

// /* =====================================================
    //                  * 6️⃣ Update Status hr_karyawan (Tandai sudah lengkap in-nya)
    //                  * ===================================================== */
    //                 DB::table('hr_karyawan')
    //                     ->where('id', $idCard)
    //                     ->update(['in_complete' => 'Y']);
    //             }

//             DB::commit();

//             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Status berhasil diperbarui.',
    //             ]);

//         } catch (\Throwable $e) {
    //             DB::rollBack();

//             Log::error('Update Status Error', [
    //                 'message' => $e->getMessage(),
    //                 'file'    => $e->getFile(),
    //                 'line'    => $e->getLine(),
    //             ]);

//             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Terjadi kesalahan saat memproses data. Cek error log.',
    //             ], 500);
    //         }
    //     }

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
                $kodeRak = $item['kodeRak'];
                $noLoker = (int) $item['noLoker'];
                $staff   = $item['staff'];
                $idCard  = $item['idCard'];

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
                    'operator'       => auth()->user()->name ?? 'Sistem',
                    'keterangan'     => 'Karyawan Baru via HR Connect',
                    'created_at'     => now(),
                ]);

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

    // public function uploadExcel(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'excel_file' => 'required|file|mimes:xlsx,xls',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()->first()], 422);
    //     }

    //     if ($req->hasFile('excel_file')) {
    //         $file = $req->file('excel_file');

    //         if (! $file->isValid()) {
    //             return response()->json(['message' => 'File tidak valid atau rusak.'], 400);
    //         }

    //         try {
    //             if ($file->getClientOriginalExtension() !== 'xlsx') {
    //                 return response()->json(['message' => 'Format file tidak valid. Hanya menerima file .xlsx.'], 400);
    //             }

    //             Excel::import(new GaShiftIn(), $file);

    //             return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
    //         } catch (\Exception $e) {
    //             Log::error('Error during file upload: ' . $e->getMessage());

    //             return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data. ' . $e->getMessage()], 500);
    //         }
    //     }

    //     return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    // }

    // Fungsi Export Excel
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
