<?php

namespace App\Http\Controllers\HR\Ecafesedaap; // Perbaikan Namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Perbaikan Import DB

class DataScanmakanController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');

        // 1. Data untuk Tabel (Log Hari Ini)
        $dataScan = DB::table('ecafesedaap_scan')
            ->whereDate('tanggal', $today)
            ->orderBy('waktu', 'desc')
            ->get();

        // 2. Ambil Quota/Pesanan hari ini dari tabel ecafesedaapbas
        $quotas = DB::table('ecafesedaapbas')
            ->whereDate('tanggal', $today)
            ->get();

        // 3. Hitung Aggregation (Actual vs Quota)
        $summary = [
            'total_scan' => $dataScan->count(),
            'total_quota' => $quotas->sum('jumlah'),
            'lebihan' => 0,
            'per_kategori' => [
                'staff' => $dataScan->where('kategori', 'staff')->count(),
                'non-staff' => $dataScan->where('kategori', 'non-staff')->count(),
            ]
        ];

        // Hitung Lebihan per Kategori & Shift
        // Kelompokkan scan hari ini untuk perbandingan
        $actualGrouped = $dataScan->groupBy(function($item) {
            return $item->kategori . '-' . $item->shift;
        });

        foreach ($quotas as $q) {
            $key = $q->kategori . '-' . $q->shift;
            $actualCount = isset($actualGrouped[$key]) ? $actualGrouped[$key]->count() : 0;
            
            if ($actualCount > $q->jumlah) {
                $summary['lebihan'] += ($actualCount - $q->jumlah);
            }
        }

        // 4. Kirim data ke view
        return view('hr.cateringbas.upload-pesanan.index', compact('dataScan', 'summary'));
    }
}

