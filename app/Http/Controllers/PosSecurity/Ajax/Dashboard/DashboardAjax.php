<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DashboardAjax extends Controller
{
    public function filter()
    {
        try {
            $query = $this->baseQuery();

            $kartuAktif = (clone $query)
                ->where('kartu_dikembalikan', 0)
                ->where(function ($q) {
                    $q->whereNull('kartu_hilang')
                        ->orWhere('kartu_hilang', 0);
                })
                ->count();

            $belumDikembalikan = (clone $query)
                ->where('kartu_dikembalikan', 0)
                ->count();

            $sudahDikembalikan = (clone $query)
                ->where('kartu_dikembalikan', 1)
                ->count();

            $totalPengunjung = (clone $query)->count();

            return response()->json([
                [
                    'icon' => 'bi-person-check',
                    'label' => 'Kartu Aktif',
                    'value' => $kartuAktif,
                    'color' => 'primary',
                ],
                [
                    'icon' => 'bi-clock-history',
                    'label' => 'Belum Dikembalikan',
                    'value' => $belumDikembalikan,
                    'color' => 'warning',
                ],
                [
                    'icon' => 'bi-check-circle',
                    'label' => 'Sudah Dikembalikan',
                    'value' => $sudahDikembalikan,
                    'color' => 'success',
                ],
                [
                    'icon' => 'bi-people',
                    'label' => 'Total Pengunjung',
                    'value' => $totalPengunjung,
                    'color' => 'info',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard filter error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function statistikPerusahaanDepartemen(Request $request)
    {
        try {
            $periode = $request->input('periode', 'all');

            $query = $this->applyPeriodeFilter(
                $this->baseQuery(),
                $periode
            );

            return response()->json([
                'perusahaan' => $this->perusahaanTeratas($query),
                'departemen' => $this->departemenFavorit($query),
            ]);
        } catch (\Exception $e) {
            Log::error('Statistik error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function baseQuery()
    {
        $transaction = DB::table('ga_visitor_transaction')
            ->select([
                'id',
                'namacomp',
                'hostdeptid',
                'kartu_dikembalikan',
                DB::raw('NULL as kartu_hilang'),
                DB::raw("'transaction' as source"),
                'created_at',
            ]);

        $vendor = DB::table('ga_visitor_vendor')
            ->select([
                'id',
                'namacomp',
                'hostdeptid',
                'kartu_dikembalikan',
                'kartu_hilang',
                DB::raw("'vendor' as source"),
                'created_at',
            ]);

        return DB::query()->fromSub(
            $transaction->unionAll($vendor),
            'visitors'
        );
    }

    private function perusahaanTeratas($query)
    {
        return (clone $query)
            ->select('namacomp as nama', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('namacomp')
            ->where('namacomp', '!=', '')
            ->groupBy('namacomp')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();
    }

    private function departemenFavorit($query)
    {
        return (clone $query)
            ->select('hostdeptid as nama', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('hostdeptid')
            ->where('hostdeptid', '!=', '')
            ->groupBy('hostdeptid')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();
    }

    private function applyPeriodeFilter($query, $periode)
    {
        $now = now();

        switch ($periode) {
            case 'today':
                return $query->whereDate('created_at', $now->toDateString());

            case 'this_week':
                return $query->whereBetween('created_at', [
                    $now->copy()->startOfWeek()->startOfDay(),
                    $now->copy()->endOfWeek()->endOfDay(),
                ]);

            case 'this_month':
                return $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth()->startOfDay(),
                    $now->copy()->endOfMonth()->endOfDay(),
                ]);

            default:
                return $query;
        }
    }
}
