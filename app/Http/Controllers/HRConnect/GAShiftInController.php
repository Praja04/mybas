<?php
namespace App\Http\Controllers\HRConnect;

use App\Exports\HRConnect\KaryawanAktifExport;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Models\Loker\Penghuni;
use App\Models\Loker\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class GAShiftInController extends Controller
{
    public function index()
    {
        $title = 'GA - Karyawan Masuk';

        // Hanya tarik tanggal dari karyawan yang BELUM selesai urusan lokernya
        // $tanggalTersedia = HrKaryawan::where([
        //     'is_excuse_out' => 'N',
        //     'in_kode_group' => 'Y',
        //     'in_complete'   => 'N',
        //     'p_no'          => 'N',
        //     'active'        => 'Y',
        //     'shutdown'      => 'N',
        // ])
        //     ->whereNotNull('tanggal_masuk')
        //     ->where('tanggal_masuk', '!=', '0000-00-00')
        //     ->distinct()
        //     ->orderBy('tanggal_masuk', 'desc')
        //     ->pluck('tanggal_masuk');

        // ->whereDate('tanggal_masuk', '>', '2024-09-30')
        // ->whereNotNull('tanggal_masuk')
        // ->select('tanggal_masuk')
        // ->distinct()
        // ->orderBy('tanggal_masuk', 'desc')
        // ->pluck('tanggal_masuk');

        // OPTIMASI: Tarik Rak dan hitung total penghuni langsung via DB Raw (Gak bikin RAM Bengkak)
        $allLokersPria   = Rak::where('kode_rak', 'LP')->where('is_active', 'Y')->get();
        $allLokersWanita = Rak::where('kode_rak', 'LW')->where('is_active', 'Y')->get();

        $penghuniPria   = Penghuni::where('kode_rak', 'LP')->where('is_active', 'Y')->get()->groupBy('no_loker');
        $penghuniWanita = Penghuni::where('kode_rak', 'LW')->where('is_active', 'Y')->get()->groupBy('no_loker');

        $lokerPria   = $this->filterLoker($allLokersPria, $penghuniPria);
        $lokerWanita = $this->filterLoker($allLokersWanita, $penghuniWanita);

        return view('hr-connect.ga.shift-in', compact('title', 'lokerPria', 'lokerWanita'));
    }

    // Helper Function untuk Filter Loker biar Controller Gak Berantakan
    private function filterLoker($allLokers, $penghuniGrouped)
    {
        return $allLokers->map(function ($rak) use ($penghuniGrouped) {
            $penghuni            = $penghuniGrouped->get($rak->no_loker) ?? collect();
            $rak->total_penghuni = $penghuni->count();

            $kategoriPenghuni = $penghuni->first() ? strtolower(trim($penghuni->first()->kategori_karyawan)) : null;

            if ($kategoriPenghuni && ! in_array($kategoriPenghuni, ['staff', 'non_staff', 'mitra_kerja', 'mitra'])) {
                $kategoriPenghuni = 'non_staff';
            }

            $rak->kategori_tersedia = $kategoriPenghuni;
            $rak->divisi_tersedia   = $penghuni->first() ? strtoupper(trim($penghuni->first()->divisi)) : null;

            return $rak;
        })->filter(function ($rak) {
            if ($rak->kategori_tersedia == 'staff' && $rak->total_penghuni >= 1) {
                return false;
            }

            if (in_array($rak->kategori_tersedia, ['mitra_kerja', 'mitra'])) {
                return false;
            }

            if ($rak->total_penghuni >= $rak->kapasitas) {
                return false;
            }

            return true;
        })->values();
    }

    public function getData(Request $req)
    {
        $query = HrKaryawan::with('penghuni')->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
                ->from('loker_penghuni')
                ->whereRaw('loker_penghuni.nik COLLATE utf8mb4_unicode_ci = hr_karyawan.nik')
                ->where('is_active', 'Y');
        })
            ->select('id', 'nik', 'nama', 'kode_divisi', 'kode_bagian', 'kode_admin', 'jenis_kelamin', 'staff', 'tanggal_masuk', 'in_complete', 'cardnodevice')
            ->where([
                'is_excuse_out' => 'N',
                'in_kode_group' => 'N',
                'in_complete'   => 'N',
                'p_no'          => 'N',
                'active'        => 'Y',
                'shutdown'      => 'N',
            ])
            ->orderBy('tanggal_masuk', 'desc');
        // ->whereDate('tanggal_masuk', '>', '2024-09-30');

        // if ($req->tampilkan_semua == 0 && ! empty($req->tanggal)) {
        //     $query->where('tanggal_masuk', $req->tanggal);
        // }

        $query->orderBy('tanggal_masuk', 'desc');

        return Datatables::of($query)
            ->addColumn('checkStaff', function ($row) {
                if ($row->penghuni) {
                    return strtolower($row->penghuni->kategori_karyawan) == 'staff' ? 'Y' : 'N';
                }
                return $row->staff;
            })
            ->make(true);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->input('data');

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dikirim',
            ], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                $nik     = $item['nik'];
                $kodeRak = $item['kodeRak'] ?? null;
                $noLoker = $item['noLoker'] ?? null;
                $staff   = $item['staff'];

                if (! empty($kodeRak) && ! empty($noLoker)) {
                    $hasActive = DB::table('loker_penghuni')
                        ->where('nik', $nik)
                        ->where('is_active', 'Y')
                        ->lockForUpdate()
                        ->exists();

                    if ($hasActive) {
                        DB::rollback();
                        return response()->json([
                            'success' => false,
                            'message' => "Karyawan NIK {$nik} sudah memiliki loker aktif",
                        ], 422);
                    }

                    $penghuniAktif = DB::table('loker_penghuni')
                        ->where('kode_rak', $kodeRak)
                        ->where('no_loker', $noLoker)
                        ->where('is_active', 'Y')
                        ->lockForUpdate()
                        ->get();

                    $existingCount = $penghuniAktif->count();

                    $divisiFix = strtoupper(trim($item['divisi']));
                    if ($staff === 'non_staff') {
                        if (strpos($divisiFix, 'PRD') !== false || strpos($divisiFix, 'PRODUKSI') !== false || strpos($divisiFix, 'PRO') !== false) {
                            $divisiFix = 'PRD BAS';
                        } elseif (strpos($divisiFix, 'QC') !== false || strpos($divisiFix, 'QUALITY') !== false || strpos($divisiFix, 'QCB') !== false) {
                            $divisiFix = 'QCB BAS';
                        }
                    }

                    if ($existingCount > 0) {
                        foreach ($penghuniAktif as $penghuni) {
                            $tipePenghuni = $penghuni->kategori_karyawan ? strtolower(trim($penghuni->kategori_karyawan)) : null;

                            if ($tipePenghuni && ! in_array($tipePenghuni, ['staff', 'non_staff', 'mitra_kerja', 'mitra'])) {
                                $tipePenghuni = 'non_staff';
                            }

                            if ($tipePenghuni !== $staff) {
                                DB::rollBack();
                                return response()->json([
                                    'success' => false,
                                    'message' => "Loker {$kodeRak}-{$noLoker} khusus untuk " . strtoupper($tipePenghuni) . ".",
                                ], 422);
                            }

                            if ($staff === 'non_staff') {
                                $divisiPenghuni = $penghuni->divisi ? strtoupper(trim($penghuni->divisi)) : '';

                                if (strpos($divisiPenghuni, 'PRD') !== false || strpos($divisiPenghuni, 'PRODUKSI') !== false || strpos($divisiPenghuni, 'PRO') !== false) {
                                    $divisiPenghuni = 'PRD BAS';
                                } elseif (strpos($divisiPenghuni, 'QC') !== false || strpos($divisiPenghuni, 'QUALITY') !== false || strpos($divisiPenghuni, 'QCB') !== false) {
                                    $divisiPenghuni = 'QCB BAS';
                                }

                                if ($divisiPenghuni !== $divisiFix) {
                                    DB::rollBack();
                                    return response()->json([
                                        'success' => false,
                                        'message' => "Gagal! Loker {$kodeRak}-{$noLoker} sudah diisi oleh divisi lain ({$divisiPenghuni}). Harap pilih divisi yang sama.",
                                    ], 422);
                                }
                            }
                        }
                    }

                    $maxCapacity = ($staff === 'staff') ? 1 : 2;
                    if ($existingCount >= $maxCapacity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Kapasitas Loker {$kodeRak}-{$noLoker} sudah penuh.",
                        ], 422);
                    }

                    DB::table('loker_penghuni')->insert([
                        'nik'               => $nik,
                        'nama'              => $item['nama'],
                        'divisi'            => $divisiFix,
                        'kode_rak'          => $kodeRak,
                        'no_loker'          => $noLoker,
                        'kategori_karyawan' => $staff,
                        'is_active'         => 'Y',
                        'tgl_masuk'         => now()->format('Y-m-d'),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    $this->catatHistoryLoker($nik, $item['nama'], $kodeRak, $noLoker, 'MASUK', 'Karyawan Baru via HR Connect');
                }

                HrKaryawan::where('id', $item['idCard'])->update([
                    'in_complete' => 'Y',
                ]);
            }

            Cache::forget('list_bulan_karyawan_masuk_ga');

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Validasi loker & Goodie Bag berhasil disimpan.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function catatHistoryLoker($nik, $nama, $kodeRak, $noLoker, $tipe, $keterangan)
    {
        DB::table('loker_transaksi')->insert([
            'nik'            => $nik, 'nama'              => $nama, 'kode_rak' => $kodeRak, 'no_loker' => $noLoker,
            'tipe_transaksi' => $tipe, 'operator'         => auth()->user()->name ?? 'Sistem GA',
            'keterangan'     => $keterangan, 'created_at' => now(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $byDate  = $request->input('tanggal');
        $showAll = $request->input('tampilkan_semua');

        $fileName = 'Data Karyawan Baru - ' . (! empty($byDate) ? 'Per Tanggal ' . $byDate : 'Data Keseluruhan') . '.xlsx';
        return Excel::download(new KaryawanAktifExport($byDate, $showAll), $fileName);
    }
}
