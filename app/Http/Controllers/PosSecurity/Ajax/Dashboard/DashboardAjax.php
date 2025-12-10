<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DashboardAjax extends Controller
{

    public function filter(Request $request)
    {
        try {
            // Ambil parameter dari form data (POST)
            $jenisKartu = $request->input('jenis_kartu', '');
            $pos = $request->input('pos', '');

            // Tentukan tabel berdasarkan POS
            $table = $this->getTableName($pos);

            // Query dasar
            $query = DB::table($table);

            // Filter berdasarkan jenis kartu (beda logika untuk tiap tabel)
            if (!empty($jenisKartu)) {
                if ($table === 'ga_visitor_vendor') {
                    // Tabel vendor: filter berdasarkan kolom 'type'
                    $query->where('type', $jenisKartu);
                } else {
                    // Tabel transaction: khusus untuk Transporter (BONGKAR/MUAT)
                    if ($jenisKartu === 'Transporter') {
                        // Filter untuk transporter berdasarkan purpose
                        $query->where(function ($q) {
                            $q->where('purpose', 'like', '%BONGKAR%')
                                ->orWhere('purpose', 'like', '%MUAT%');
                        });
                    } else {
                        // Jika memilih Vendor atau Tamu, tidak ada data karena 
                        // tabel transaction hanya untuk Transporter
                        $query->whereRaw('1 = 0'); // Query yang tidak akan mengembalikan hasil
                    }
                }
            }

            // Hitung statistik berdasarkan query yang sudah difilter
            $stats = $this->calculateStats($query, $table);

            // Format response - GANTI KARTU HILANG MENJADI SUDAH DIKEMBALIKAN
            $data = [
                [
                    'icon' => 'bi-person-check',
                    'label' => 'Kartu Aktif',
                    'value' => $stats['kartu_aktif'],
                    'color' => 'primary'
                ],
                [
                    'icon' => 'bi-clock-history',
                    'label' => 'Belum Dikembalikan',
                    'value' => $stats['belum_dikembalikan'],
                    'color' => 'warning'
                ],
                [
                    'icon' => 'bi-check-circle',
                    'label' => 'Sudah Dikembalikan',
                    'value' => $stats['kartu_hilang'], // GANTI NAMA LABEL SAJA
                    'color' => 'success'
                ],
                [
                    'icon' => 'bi-people',
                    'label' => 'Total Pengunjung',
                    'value' => $stats['total_pengunjung'],
                    'color' => 'info'
                ]
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Dashboard filter error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getTableName($pos)
    {
        // Normalisasi input POS: trim dan hapus spasi
        $normalizedPos = str_replace(' ', '', strtolower(trim($pos ?? '')));

        if ($normalizedPos === 'pos2') {
            return 'ga_visitor_vendor';
        }

        // Default to POS 1
        return 'ga_visitor_transaction';
    }

    private function calculateStats($query, $table)
    {
        // Buat query terpisah untuk setiap perhitungan
        $kartuAktifQuery = clone $query;
        $belumDikembalikanQuery = clone $query;
        $sudahDikembalikanQuery = clone $query; // GANTI NAMA VARIABLE
        $totalQuery = clone $query;

        if ($table === 'ga_visitor_vendor') {
            // Untuk tabel vendor
            $kartuAktif = $kartuAktifQuery
                ->where('kartu_dikembalikan', 0)  // Belum dikembalikan
                ->where('kartu_hilang', 0)        // Tidak hilang
                ->count();

            $belumDikembalikan = $belumDikembalikanQuery
                ->where('kartu_dikembalikan', 0)  // Belum dikembalikan
                ->count();

            $sudahDikembalikan = $sudahDikembalikanQuery // GANTI NAMA VARIABLE
                ->where('kartu_dikembalikan', 1)  // SUDAH DIKEMBALIKAN
                ->count();
        } else {
            // Untuk tabel transaction
            $kartuAktif = $kartuAktifQuery
                ->where('kartu_dikembalikan', 0)  // Belum dikembalikan
                ->whereNull('tanggal_lapor_hilang') // Tidak hilang
                ->count();

            $belumDikembalikan = $belumDikembalikanQuery
                ->where('kartu_dikembalikan', 0)  // Belum dikembalikan
                ->count();

            $sudahDikembalikan = $sudahDikembalikanQuery // GANTI NAMA VARIABLE
                ->where('kartu_dikembalikan', 1)  // SUDAH DIKEMBALIKAN
                ->count();
        }

        $totalPengunjung = $totalQuery->count();

        return [
            'kartu_aktif' => $kartuAktif,
            'belum_dikembalikan' => $belumDikembalikan,
            'kartu_hilang' => $sudahDikembalikan, // GANTI NAMA KEY
            'total_pengunjung' => $totalPengunjung
        ];
    }

    // filter departemen
    public function statistikPerusahaanDepartemen(Request $request)
    {
        try {
            $periode = $request->input('periode', 'all');
            $jenisKartu = $request->input('jenis_kartu', '');
            $pos = $request->input('pos', 'POS 1');

            // Tentukan tabel berdasarkan POS
            $table = $this->getTableName($pos);

            // Query dasar dengan periode
            $query = $this->applyPeriodeFilter(DB::table($table), $periode);

            // Filter berdasarkan jenis kartu
            if (!empty($jenisKartu)) {
                if ($table === 'ga_visitor_vendor') {
                    $query->where('type', $jenisKartu);
                } else {
                    if ($jenisKartu === 'Transporter') {
                        $query->where(function ($q) {
                            $q->where('purpose', 'like', '%BONGKAR%')
                                ->orWhere('purpose', 'like', '%MUAT%');
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }
            }

            // Hitung perusahaan teratas
            $perusahaan = $this->getPerusahaanTeratas($query, $table);

            // Hitung departemen favorit (berdasarkan hostdeptid)
            $departemen = $this->getDepartemenFavorit($query, $table);

            return response()->json([
                'perusahaan' => $perusahaan,
                'departemen' => $departemen
            ]);
        } catch (\Exception $e) {
            Log::error('Statistik perusahaan departemen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function applyPeriodeFilter($query, $periode)
    {
        $now = now();

        switch ($periode) {
            case 'today':
                return $query->whereDate('created_at', $now->toDateString());
            case 'this_week':
                return $query->whereBetween('created_at', [
                    $now->startOfWeek()->toDateString(),
                    $now->endOfWeek()->toDateString()
                ]);
            case 'this_month':
                return $query->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year);
            default:
                return $query; // Semua waktu
        }
    }

    private function getPerusahaanTeratas($query, $table)
    {
        $perusahaanQuery = clone $query;

        return $perusahaanQuery
            ->select('namacomp as nama', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('namacomp')
            ->where('namacomp', '!=', '')
            ->groupBy('namacomp')
            ->orderBy('jumlah', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getDepartemenFavorit($query, $table)
    {
        $departemenQuery = clone $query;

        return $departemenQuery
            ->select('hostdeptid as nama', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('hostdeptid')
            ->where('hostdeptid', '!=', '')
            ->groupBy('hostdeptid')
            ->orderBy('jumlah', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
