<?php

namespace App\Http\Controllers\PosSecurity\Datatable\Absensi;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\Absensi\AbsensiRestLog;

class AbsensiDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->drawTable($query);
    }

    private function rawData($request)
    {
        $filter = $request->input('filter', []);

        // Log::info('Filter received:', $filter); // Opsional: debug

        $query = AbsensiRestLog::query()
            ->orderByDesc('scan_time')
            ->orderByDesc('created_at');

        // ✅ Nama Visitor: cari di nama, perusahaan (namacomp), dan tujuan (purpose)
        if (!empty($filter['nama_visitor'])) {
            $search = trim($filter['nama_visitor']);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('namacomp', 'like', '%' . $search . '%')
                    ->orWhere('purpose', 'like', '%' . $search . '%');
            });
        }

        // Filter: No. Kartu → juga cari di No Identitas (KTP/SIM)
        if (!empty($filter['no_kartu'])) {
            $search = trim($filter['no_kartu']);
            $query->where(function ($q) use ($search) {
                $q->where('no_kartu', 'like', '%' . $search . '%')
                    ->orWhere('no_ktp_sim', 'like', '%' . $search . '%');
            });
        }

        // Filter: Jenis Kartu (vendor, tamu, transporter)
        if (!empty($filter['type'])) {
            $query->where('type', $filter['type']);
        }

        // Filter: Kartu Dikembalikan
        if (isset($filter['kartu_dikembalikan'])) {
            $query->where('kartu_dikembalikan', (int)$filter['kartu_dikembalikan']);
        }

        // Filter: Rentang Tanggal
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
            $end = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->endOfDay();

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('scan_time', [$start, $end])
                    ->orWhereNull('scan_time')
                    ->whereBetween('tanggal_log', [$start, $end]);
            });
        } elseif (!empty($filter['start_date'])) {

            $date = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
            $end  = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->endOfDay();

            $query->where(function ($q) use ($date, $end) {
                $q->whereBetween('scan_time', [$date, $end])
                ->orWhereBetween('tanggal_log', [$date, $end]);
            });
        }

        return $query->limit(500)->get();
    }

    /**
     * Render DataTable
     */
    private function drawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama', fn($item) => e($item->nama ?? '-'))
            ->editColumn('no_kartu', fn($item) => e($item->no_kartu ?? '-'))
            ->editColumn('no_ktp_sim', fn($item) => e($item->no_ktp_sim ?? '-'))
            ->editColumn('namacomp', fn($item) => e($item->namacomp ?? '-'))
            ->editColumn('host', fn($item) => e($item->host ?? '-'))
            ->editColumn('purpose', fn($item) => e($item->purpose ?? '-'))
            ->editColumn('nopol', fn($item) => e($item->nopol ?? '-'))
            ->editColumn('plant', fn($item) => e($item->plant ?? '-'))
            ->addColumn('jenis_tamu', function ($item) {
                switch ($item->source_origin) {
                    case 'ga_visitor_transactions':
                        return '<span class="badge bg-primary">SUPPLIER/TRANSPORTER</span>';
                    case 'ga_visitor_vendor_transactions':
                        return '<span class="badge bg-success">TAMU/VENDOR</span>';
                    default:
                        return '<span class="badge bg-secondary">' . e(ucwords(str_replace('ga_', '', str_replace('_', ' ', $item->source_origin)))) . '</span>';
                }
            })
            ->editColumn('tgl_lahir', function ($item) {
                if (!$item->tgl_lahir) {
                    return '-';
                }

                try {
                    $date = \Illuminate\Support\Carbon::parse($item->tgl_lahir);
                    return $date->format('d-m-Y');
                } catch (\Exception $e) {
                    return '-';
                }
            })
            ->editColumn('scan_time', function ($item) {
                if (!$item->scan_time) {
                    return '-';
                }

                try {
                    $date = \Illuminate\Support\Carbon::parse($item->scan_time);
                    return $date->format('d-m-Y H:i:s');
                } catch (\Exception $e) {
                    return '-';
                }
            })
            ->addColumn('photo_visitor', function ($item) {
                if (empty($item->foto)) {
                    return '<span class="text-muted">Tidak ada</span>';
                }

                // Karena $item->foto adalah string URL, langsung pakai
                $url = $item->foto;

                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    return '
        <img src="' . e($url) . '" 
             alt="Foto Diri" 
             style="max-width: 80px; max-height: 80px; border-radius: 6px; cursor: pointer; margin: 3px;" 
             onclick="showImageModal(\'' . e(addslashes($url)) . '\')"
             class="shadow-sm">
        ';
                }

                return '<span class="text-muted">Invalid URL</span>';
            })
            ->addColumn('img_visitor', function ($item) {
                if (empty($item->imgvisitorpathin)) {
                    return '<span class="text-muted">Tidak ada</span>';
                }

                $url = $item->imgvisitorpathin;

                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    return '
        <img src="' . e($url) . '" 
             alt="Foto KTP" 
             style="max-width: 80px; max-height: 80px; border-radius: 6px; cursor: pointer; margin: 3px;" 
             onclick="showImageModal(\'' . e(addslashes($url)) . '\')"
             class="shadow-sm">
        ';
                }

                return '<span class="text-muted">Invalid URL</span>';
            })
            ->editColumn('activity_type', function ($item) {
                $label = $item->activity_type === 'in'
                    ? '<span class="badge bg-primary">Masuk</span>'
                    : '<span class="badge bg-warning text-dark">Keluar</span>';
                return $item->activity_type ? $label : '-';
            })
            ->editColumn('kartu_dikembalikan', function ($item) {
                return $item->kartu_dikembalikan
                    ? '<span class="badge bg-success">Ya</span>'
                    : '<span class="badge bg-secondary">Tidak</span>';
            })
            ->addColumn('action', function ($item) {
                return '
                <div class="dropdown d-inline-block">
                    <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="javascript:void(0)" class="dropdown-item" onclick="openAbsensiDetailModal(' . $item->id . ')">
                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Lihat Detail
                            </a>
                        </li>
                    </ul>
                </div>';
            })
            ->rawColumns(['jenis_tamu', 'activity_type', 'kartu_dikembalikan', 'photo_visitor', 'img_visitor', 'action'])
            ->make(true);
    }
}
