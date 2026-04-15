<?php
namespace App\Http\Controllers;

use App\Exports\LokerExport;
use App\Http\Controllers\Controller;
use App\Imports\LokerImport;
use App\Models\HR\Karyawan;
use App\Models\Loker\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LokerV2Controller extends Controller
{
    /**
     * Helper untuk standarisasi Prefix (LP/LW)
     */
    private function getPrefix($gender)
    {
        $g = strtoupper($gender);
        return ($g == 'L' || $g == 'PRIA' || $g == 'LAKI-LAKI' || $g == 'LP') ? 'LP' : 'LW';
    }

    /**
     * Helper Kategori Karyawan menyesuaikan struktur tabel hr_karyawan
     */
    private function getKategoriKaryawan($karyawan)
    {
        if (! $karyawan) {
            return 'non_staff';
        }

        // 1. Cek Staff berdasarkan kolom staff (enum 'Y'/'N')
        if (strtoupper($karyawan->staff ?? 'N') === 'Y') {
            return 'staff';
        }

        // 2. Cek Mitra Kerja berdasarkan kode_divisi atau kode_bagian
        $divisi = strtoupper($karyawan->kode_divisi ?? '');
        $bagian = strtoupper($karyawan->kode_bagian ?? '');

        if (str_contains($divisi, 'MITRA') || str_contains($bagian, 'MITRA')) {
            return 'mitra_kerja';
        }

        // 3. Fallback ke Non-Staff
        return 'non_staff';
    }

    public function index()
    {
        $genders       = ['L' => 'Pria', 'P' => 'Wanita'];
        $dashboardData = [];
        $grandTotal    = ['total' => 0, 'penuh' => 0, 'tersedia' => 0, 'rusak' => 0];

        foreach ($genders as $code => $label) {
            $prefix  = ($code == 'L') ? 'LP' : 'LW';
            $lockers = Rak::where('kode_rak', $prefix)->get();

            $allPenghuni = DB::table('loker_penghuni')
                ->where('kode_rak', $prefix)
                ->where('is_active', 'Y')
                ->whereNull('tgl_keluar')
                ->get()
                ->groupBy('no_loker');

            $processed = $lockers->map(function ($rak) use ($allPenghuni) {
                $penghuniLoker = $allPenghuni->get($rak->no_loker) ?? collect();
                $count         = $penghuniLoker->count();

                // Ambil kategori dari penghuni yang sudah ada
                $penghuniPertama = $penghuniLoker->first();
                $kategori        = $penghuniPertama ? strtolower(trim($penghuniPertama->kategori_karyawan)) : 'non_staff';

                // Logic Kapasitas: STAFF max 1, NON-STAFF sesuai kapasitas rak (default 2)
                $maxDisplay = ($kategori == 'staff') ? 1 : (int) $rak->kapasitas;
                $status     = ($count >= $maxDisplay) ? 'penuh' : 'tersedia';

                if ($rak->is_active == 'N') {
                    $status = 'rusak';
                }

                return [
                    'id'       => $rak->id,
                    'no'       => $rak->no_loker,
                    'count'    => $count,
                    'max'      => $maxDisplay,
                    'status'   => $status,
                    'gender'   => $rak->kode_rak,
                    'kategori' => $kategori,
                ];
            })->sortBy(fn($item) => (int) $item['no']);

            $stats = [
                'total'    => $processed->count(),
                'penuh'    => $processed->where('status', 'penuh')->count(),
                'tersedia' => $processed->where('status', 'tersedia')->count(),
                'rusak'    => $processed->where('status', 'rusak')->count(),
            ];

            foreach ($stats as $key => $val) {
                $grandTotal[$key] += $val;
            }

            $dashboardData[$label] = ['lockers' => $processed, 'stats' => $stats];
        }

        return view('loker.index', compact('dashboardData', 'grandTotal'));
    }

    public function searchKaryawan($search)
    {
        $search = trim($search);

        $lokerAktif = DB::table('loker_penghuni')
            ->where('is_active', 'Y')
            ->whereNull('tgl_keluar')
            ->where(function ($query) use ($search) {
                $query->where('nik', $search)
                    ->orWhere('nama', 'LIKE', "%{$search}%");
            })->first();

        $karyawan = Karyawan::where('nik', $search)
            ->orWhere('nama', 'LIKE', "%{$search}%")
            ->orWhere('cardnodevice', $search)
            ->first();

        if (! $karyawan && ! $lokerAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan! Pastikan NIK atau Nama benar.',
            ]);
        }

        $nik      = $lokerAktif->nik ?? ($karyawan->nik ?? '-');
        $nama     = $lokerAktif->nama ?? ($karyawan->nama ?? 'Tidak Dikenali');
        $kategori = $lokerAktif ? $lokerAktif->kategori_karyawan : $this->getKategoriKaryawan($karyawan);

        if ($lokerAktif) {
            $gender = ($lokerAktif->kode_rak == 'LP') ? 'L' : 'P';
        } else {
            $gender = strtoupper($karyawan->jenis_kelamin ?? 'L');
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'nik'         => $nik,
                'nama'        => $nama,
                'gender'      => $gender,
                'kategori'    => $kategori,
                'divisi'      => $lokerAktif->divisi ?? ($karyawan->kode_divisi ?? '-'),
                // PERBAIKAN DI SINI: Samakan key dengan yang dipanggil Javascript
                'no_loker'    => $lokerAktif ? $lokerAktif->no_loker : null,
                'kode_rak'    => $lokerAktif ? $lokerAktif->kode_rak : null,
                'status_hris' => ($karyawan && $karyawan->active == 'Y') ? 'Aktif' : 'Tidak Aktif',
            ],
        ]);
    }

    public function apiSuggestLoker(Request $request)
    {
        $prefix = $this->getPrefix($request->gender);
        // Gunakan kategori dari request jika NIK tidak ketemu di Karyawan (untuk manual)
        $karyawan = Karyawan::where('nik', trim($request->nik))->first();
        $kategori = $karyawan ? $this->getKategoriKaryawan($karyawan) : $request->kategori;

        $suggest = Rak::where('kode_rak', $prefix)
            ->where('is_active', 'Y')
            ->where(function ($q) {
                $q->whereNull('keterangan_kondisi')
                    ->orWhere('keterangan_kondisi', 'NOT LIKE', '%Rusak%');
            })
        // Proteksi Kapasitas & Kategori
            ->where(function ($q) use ($kategori, $prefix) {
                $subQuery = "SELECT COUNT(*) FROM loker_penghuni
                         WHERE loker_penghuni.no_loker = loker_rak.no_loker
                         AND loker_penghuni.kode_rak = '$prefix'
                         AND loker_penghuni.is_active = 'Y'";

                if ($kategori === 'staff') {
                    $q->whereRaw("($subQuery) = 0");
                } else {
                    // Non-staff boleh di loker yang isinya < 2 DAN tidak ada staff di dalamnya
                    $q->whereRaw("($subQuery) < 2")
                        ->whereNotExists(function ($sq) use ($prefix) {
                            $sq->select(DB::raw(1))
                                ->from('loker_penghuni')
                                ->whereRaw('loker_penghuni.no_loker = loker_rak.no_loker')
                                ->where('loker_penghuni.kode_rak', $prefix)
                                ->where('loker_penghuni.is_active', 'Y')
                                ->where('loker_penghuni.kategori_karyawan', 'staff');
                        });
                }
            })
            ->orderByRaw('no_loker ASC')
            ->first();

        return response()->json([
            'status'            => 'success',
            'rekomendasi_loker' => $suggest ? $suggest->no_loker : 'penuh',
        ]);
    }

    public function getAvailableLockers($gender, $kategori = 'non_staff')
    {
        $prefix = $this->getPrefix($gender);

        $available = DB::table('loker_rak')
            ->where('kode_rak', $prefix)
            ->where('is_active', 'Y')
            ->where(function ($q) use ($prefix, $kategori) {
                $subCount = "SELECT COUNT(*) FROM loker_penghuni
                         WHERE loker_penghuni.no_loker = loker_rak.no_loker
                         AND loker_penghuni.kode_rak = '$prefix'
                         AND loker_penghuni.is_active = 'Y'";

                $hasStaff = "SELECT COUNT(*) FROM loker_penghuni
                WHERE loker_penghuni.no_loker = loker_rak.no_loker
                AND loker_penghuni.kode_rak = '$prefix'
                AND loker_penghuni.is_active = 'Y'
                AND loker_penghuni.kategori_karyawan = 'staff'";

                // Tampilkan semua yang belum penuh (max 2)
                if ($kategori == 'staff') {
                    $q->whereRaw("($subCount) = 0");
                } else {
                    $q->whereRaw("($subCount) < 2")
                        ->whereRaw("($hasStaff) = 0");
                }
            })
            ->orderByRaw('CAST(no_loker AS UNSIGNED) ASC')
            ->get(['no_loker']);

        return response()->json($available);
    }

    public function getDetailLoker($gender, $no_loker)
    {
        $prefix = $this->getPrefix($gender);

        $data = DB::table('loker_penghuni')
            ->where('kode_rak', $prefix)
            ->where('no_loker', $no_loker)
            ->whereNull('tgl_keluar')
            ->where('is_active', 'Y')
            ->select('id', 'nik', 'nama', 'divisi', 'kategori_karyawan', 'tgl_masuk')
            ->get()
            ->map(function ($item) {
                $item->nik       = $item->nik ?? '-';
                $item->nama      = $item->nama ?? '-';
                $item->divisi    = $item->divisi ?? '-';
                $item->kategori  = $item->kategori_karyawan ? str_replace('_', ' ', strtoupper($item->kategori_karyawan)) : '-';
                $item->tgl_masuk = $item->tgl_masuk ? date('d-m-Y', strtotime($item->tgl_masuk)) : '-';
                return $item;
            });

        return response()->json($data);
    }

    public function updateStatus(Request $request)
    {
        $prefix = $this->getPrefix($request->gender);
        $status = $request->status;

        $description = ($status == 'rusak') ? 'Maintenance/Rusak (' . date('d/m/Y H:i') . ')' : null;

        $affected = DB::table('loker_rak')
            ->where('kode_rak', $prefix)
            ->where('no_loker', trim($request->no_loker))
            ->update([
                'is_active'          => ($request->status == 'rusak') ? 'N' : 'Y',
                'keterangan_kondisi' => $description,
                'updated_at'         => now(),
            ]);

        return $affected ? response()->json(['status' => 'success']) : response()->json(['status' => 'error'], 404);
    }

    public function tarikKunci(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $p = DB::table('loker_penghuni')->where('id', $request->id)->first();
            if (! $p) {
                return response()->json(['status' => 'error'], 422);
            }

            $cekPenghuni = DB::table('loker_penghuni')
                ->where('no_loker', $p->no_loker)
                ->where('kode_rak', $p->kode_rak)
                ->where('is_active', 'Y')
                ->where('id', '!=', $request->id)
                ->whereNull('tgl_keluar')
                ->first();

            $keterangan = $cekPenghuni ? 'Terisi: ' . $cekPenghuni->nik . ' - ' . $cekPenghuni->nama : null;

            DB::table('loker_rak')
                ->where('kode_rak', $p->kode_rak)
                ->where('no_loker', $p->no_loker)
                ->update([
                    'keterangan_kondisi' => $keterangan,
                    'updated_at'         => now(),
                ]);

            DB::table('loker_transaksi')->insert([
                'nik'            => $p->nik ?? '-',
                'nama'           => $p->nama,
                'kode_rak'       => $p->kode_rak,
                'no_loker'       => $p->no_loker,
                'tipe_transaksi' => 'KELUAR',
                'operator'       => auth()->user()->name ?? 'System',
                'keterangan'     => 'Penarikan: ' . ($request->alasan ?? 'Karyawan Keluar'),
                'created_at'     => now(),
            ]);

            DB::table('loker_penghuni')->where('id', $request->id)->update([
                'is_active' => 'N', 'tgl_keluar' => now(), 'updated_at' => now(),
            ]);

            return response()->json(['status' => 'success']);
        });
    }

    public function store(Request $request)
    {
        $nik    = trim($request->nik);
        $prefix = $this->getPrefix($request->gender);

        $dataLoker = DB::table('loker_penghuni')
            ->where('nik', $nik)->where('is_active', 'Y')
            ->orderBy('created_at', 'desc')->first();

        $dataHRIS = Karyawan::where('nik', $nik)->first();

        if ($dataLoker) {
            $namaKaryawan = $dataLoker->nama;
            $divisi       = $dataLoker->divisi;
            $kategori     = $dataLoker->kategori_karyawan;
        } elseif ($dataHRIS) {
            $namaKaryawan = $dataHRIS->nama;
            $divisi       = $dataHRIS->kode_divisi ?? '-'; // Kolom sesuai tabel hr_karyawan
            $kategori     = $this->getKategoriKaryawan($dataHRIS);
        } else {
            return response()->json(['status' => 'error', 'message' => "NIK ($nik) tidak ditemukan!"], 422);
        }

        return DB::transaction(function () use ($request, $nik, $namaKaryawan, $divisi, $kategori, $prefix) {

            // Validasi Double Plotting
            $existing = DB::table('loker_penghuni')
                ->where('kode_rak', $prefix)->where('no_loker', $request->no_loker)
                ->where('is_active', 'Y')->get();

            if ($kategori === 'staff' && $existing->count() > 0) {
                return response()->json(['status' => 'error', 'message' => 'Loker Staff harus kosong!'], 422);
            }

            // Handling Relokasi Otomatis
            $lokerLama = DB::table('loker_penghuni')->where('nik', $nik)->where('is_active', 'Y')->first();
            if ($lokerLama) {
                DB::table('loker_penghuni')->where('id', $lokerLama->id)->update([
                    'is_active' => 'N', 'tgl_keluar' => now(), 'updated_at' => now(),
                ]);

                DB::table('loker_rak')
                    ->where('kode_rak', $lokerLama->kode_rak)
                    ->where('no_loker', $lokerLama->no_loker)
                    ->update([
                        'keterangan_kondisi' => null,
                        'updated_at'         => now(),
                    ]);

                DB::table('loker_transaksi')->insert([
                    'nik'      => $nik, 'nama'                                   => $namaKaryawan, 'kode_rak' => $lokerLama->kode_rak,
                    'no_loker' => $lokerLama->no_loker, 'tipe_transaksi'         => 'KELUAR (PINDAH)',
                    'operator' => auth()->user()->name ?? 'Sistem', 'created_at' => now(),
                ]);
            }

            // Simpan Data Baru
            DB::table('loker_penghuni')->insert([
                'nik'               => $nik, 'nama'           => $namaKaryawan, 'divisi' => $divisi,
                'kode_rak'          => $prefix, 'no_loker'    => $request->no_loker,
                'kategori_karyawan' => $kategori, 'tgl_masuk' => now(),
                'is_active'         => 'Y', 'created_at'      => now(), 'updated_at'     => now(),
            ]);

            DB::table('loker_rak')
                ->where('kode_rak', $prefix)
                ->where('no_loker', $request->no_loker)
                ->update([
                    'keterangan_kondisi' => "Terisi: $nik - $namaKaryawan",
                    'updated_at'         => now(),
                ]);

            DB::table('loker_transaksi')->insert([
                'nik'      => $nik, 'nama'                                       => $namaKaryawan, 'kode_rak' => $prefix,
                'no_loker' => $request->no_loker, 'tipe_transaksi'               => 'MASUK',
                'operator' => auth()->user()->username ?? 'Sistem', 'created_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Berhasil disimpan!']);
        });
    }

    public function import(Request $request)
    {
        $request->validate([
            'file'   => 'required|mimes:xlsx,xls',
            'gender' => 'required|in:L,P',
        ]);

        try {
            $gender = $request->gender;
            $prefix = ($gender == 'L') ? 'LP' : 'LW';

            DB::transaction(function () use ($request, $gender, $prefix) {
                // 1. Bersihkan data penghuni lama
                DB::table('loker_penghuni')->where('kode_rak', $prefix)->delete();

                // 2. Jalankan Import
                $importInstance = new LokerImport($gender);
                Excel::import($importInstance, $request->file('file'));

                // 3. Ambil nomor terakhir yang terbaca di Excel (Misal: 228)
                $maxLoker = (int) $importInstance->getLastLoker();

                // 4. Ambil data penghuni yang baru masuk untuk referensi mapping
                $newPenghuni = DB::table('loker_penghuni')
                    ->where('kode_rak', $prefix)
                    ->get()
                    ->groupBy('no_loker');

                // 5. Sinkronisasi Unit Loker (Loop 1 sampai Max Loker)
                for ($i = 1; $i <= $maxLoker; $i++) {
                    $noLoker = (string) $i;
                    $items   = isset($newPenghuni[$noLoker]) ? $newPenghuni[$noLoker] : null;

                    $label = null;
                    if ($items && count($items) > 0) {
                        $p     = $items[0]; // Di Laravel 7 koleksi bisa diakses via index
                        $count = count($items);
                        $label = "Terisi: " . ($p->nik ?? '-') . " - " . ($p->nama ?? '-');
                        if ($count > 1) {
                            $label .= " (+$count)";
                        }
                        $label = substr($label, 0, 250);
                    }

                    DB::table('loker_rak')->updateOrInsert(
                        ['kode_rak' => $prefix, 'no_loker' => $noLoker],
                        [
                            'gender'             => $gender,
                            'is_active'          => 'Y',
                            'keterangan_kondisi' => $label,
                            'updated_at'         => now(),
                        ]
                    );
                }

                // 6. PERBAIKAN ERROR: Matikan loker lama yang nomornya di luar jangkauan Excel
                // Di Laravel 7, gunakan whereRaw untuk casting manual
                DB::table('loker_rak')
                    ->where('kode_rak', $prefix)
                    ->whereRaw('CAST(no_loker AS UNSIGNED) > ?', [$maxLoker])
                    ->update([
                        'is_active'  => 'N',
                        'updated_at' => now(),
                    ]);
            });

            return response()->json(['status' => 'success', 'message' => 'Import sukses! ' . $gender . ' terupdate sampai nomor terakhir.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function export($gender)
    {
        $namaFile = 'Loker_' . ($gender == 'L' ? 'Pria' : 'Wanita') . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new LokerExport($gender), $namaFile);
    }

    // public function checkRfid(Request $request)
    // {
    //     $rfidInput = trim($request->rfid_uid);

    //     if (! $rfidInput) {
    //         return response()->json(['status' => 'error', 'message' => 'Input kosong'], 400);
    //     }

    //     $karyawan = DB::table('hr_karyawan')
    //         ->where('nik', $rfidInput)
    //         ->where('active', 'Y')
    //         ->first();

    //     if (! $karyawan) {
    //         $karyawan = DB::table('hr_karyawan')
    //             ->where('cardnodevice', $rfidInput)
    //             ->where('active', 'Y')
    //             ->first();
    //     }

    //     if (! $karyawan) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => "Data ($rfidInput) tidak ditemukan di kolom NIK maupun Card Device!",
    //         ], 404);
    //     }

    //     $checkLoker = DB::table('loker_penghuni')
    //         ->where('nik', $karyawan->nik)
    //         ->first();

    //     if ($checkLoker) {
    //         return response()->json([
    //             'status'   => 'has_loker',
    //             'nik'      => $karyawan->nik,
    //             'no_loker' => $checkLoker->no_loker,
    //             'gender'   => $karyawan->jenis_kelamin,
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 'new',
    //             'nik'    => $karyawan->nik,
    //             'nama'   => $karyawan->nama,
    //         ]);
    //     }
    // }

    public function getFoto($nik)
    {
        $imageData = Cache::remember("foto_karyawan_{$nik}", 3600, function () use ($nik) {
            try {
                $user = DB::connection('192.168.178.44-admin')->table('MSIDCARD')
                    ->select('FOTOBLOB')->whereRaw('CAST(BARCODE AS SIGNED) = ?', [trim($nik)])->first();
                return ($user && $user->FOTOBLOB) ? 'data:image/jpeg;base64,' . base64_encode($user->FOTOBLOB) : null;
            } catch (\Throwable $e) {return 'error';}
        });
        return response()->json(['success' => ($imageData && $imageData !== 'error'), 'image' => $imageData]);
    }
}
