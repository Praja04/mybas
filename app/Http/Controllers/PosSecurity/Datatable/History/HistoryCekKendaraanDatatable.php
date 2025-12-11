<?php

namespace App\Http\Controllers\PosSecurity\Datatable\History;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaCekKendaraan;

class HistoryCekKendaraanDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->DrawTable($query);
    }

    private function rawData($request)
    {
        $query = GaCekKendaraan::query();

        // Batasi ke 7 hari terakhir
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $query->where('created_at', '>=', $sevenDaysAgo);

        $query->orderBy('created_at', 'desc');

        return $query->limit(300)->get();
    }

    private function DrawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('waktu_pemeriksaan', function ($item) {
                if (!$item->tgl_periksa && !$item->jam_periksa) return '-';

                $tanggal = $item->tgl_periksa ? date('d-m-Y', strtotime($item->tgl_periksa)) : '-';
                $jam = $item->jam_periksa ? date('H:i', strtotime($item->jam_periksa)) : '-';

                return '<span class="d-block mb-1">📅 <strong>' . $tanggal . '</strong></span>' .
                    '<span>⏰ <strong>' . $jam . '</strong></span>';
            })
            ->addColumn('jenis', function ($item) {
                $truck  = $item->truck_type ?: '-';
                $muatan = $item->muatan_type ?: '-';

                return "<span>{$truck}</span><br><span>({$muatan})</span>";
            })
            ->editColumn('nomor_polisi', function ($item) {
                return '<strong>' . ($item->nomor_polisi ?: '-') . '</strong>';
            })
            ->editColumn('nama_supir', function ($item) {
                return $item->nama_supir ?: '-';
            })
            ->editColumn('company', function ($item) {
                return $item->company ?: '-';
            })
            ->editColumn('nama_petugas', function ($item) {
                return $item->nama_petugas ?: '-';
            })
            ->addColumn('action', function ($item) {
                return '
                    <a href="#" class="dropdown-item" onclick="openCekKendaraanActionModal(\'' . $item->trncekid . '\')">
                        <i class="ri-eye-fill align-bottom me-2 text-muted"></i>Lihat Detail
                    </a>
                ';
            })
            ->rawColumns(['nomor_polisi', 'nama_supir', 'nama_perusahaan', 'nama_petugas', 'waktu_pemeriksaan', 'jenis', 'action'])
            ->make(true);
    }
    // <div class="dropdown d-inline-block">
    //     <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    //         <i class="ri-more-fill align-middle"></i>
    //     </button>
    //     <ul class="dropdown-menu dropdown-menu-end">
    //         <li>
    //             <a href="#!" class="dropdown-item" onclick="openCekKendaraanActionModal(\'' . $item->trncekid . '\')">
    //                 <i class="ri-eye-fill align-bottom me-2 text-muted"></i>View Detail
    //             </a>
    //         </li>
    //     </ul>
    // </div>
}
