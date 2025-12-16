<?php

namespace App\Http\Controllers\PosSecurity\Datatable\Absensi;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AbsensiGateDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->drawTable($query);
    }

    private function rawData($request)
    {
        $filter = $request->query('filter', []);

        // Query utama: gabungkan gate log dan visitor log
        $query = DB::table('ga_gate_access_logs_security as security')
            ->leftJoin('ga_visitor_rest_logs as visitor', 'security.visitor_trn', '=', 'visitor.trnvisitorid')
            ->select([
                'security.id as security_log_id',
                'security.nik as security_nik',
                'security.nama as security_nama',
                'security.dept as security_dept',
                'security.gate',
                'security.waktu',
                'security.foto_url as foto_dari_gate', // opsional

                'visitor.id as visitor_id',
                'visitor.trnvisitorid',
                'visitor.nama as visitor_nama',
                'visitor.namacomp as visitor_company',
                'visitor.purpose as visitor_purpose',
                'visitor.activity_type',
                'visitor.foto as visitor_foto_url',         // foto dari visitor (base64/URL)
                'visitor.imgvisitorpathin as visitor_img_in', // path foto masuk
                'visitor.no_ktp_sim',
                'visitor.nopol',
                'visitor.plant',
            ])
            ->whereNotNull('security.visitor_trn') // hanya untuk visitor
            ->orderByDesc('security.waktu');

        // 🔍 Filter: Nama Pengunjung / Perusahaan / Tujuan
        if (!empty($filter['nama_visitor'])) {
            $search = trim($filter['nama_visitor']);
            $query->where(function ($q) use ($search) {
                $q->where('visitor.nama', 'like', '%' . $search . '%')
                    ->orWhere('visitor.namacomp', 'like', '%' . $search . '%')
                    ->orWhere('visitor.purpose', 'like', '%' . $search . '%');
            });
        }

        // 🔍 Filter: NIK Security
        if (!empty($filter['security_nik'])) {
            $search = trim($filter['security_nik']);
            $query->where('security.nik', 'like', '%' . $search . '%');
        }

        // 🔍 Filter: Nama Security
        if (!empty($filter['security_nama'])) {
            $search = trim($filter['security_nama']);
            $query->where('security.nama', 'like', '%' . $search . '%');
        }

        // 🔍 Filter: Gate
        if (!empty($filter['gate'])) {
            $query->where('security.gate', $filter['gate']);
        }

        // 🔍 Filter: Rentang Tanggal
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            try {
                $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
                $end = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->endOfDay();
                $query->whereBetween('security.waktu', [$start, $end]);
            } catch (\Exception $e) {
                Log::channel('ga_sistem_tracking')->warning('Date filter error in SecurityGateAccess: ' . $e->getMessage());
            }
        }

        return $query->limit(1000)->get();
    }

    private function drawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()

            // 🧑‍💼 Security
            ->editColumn('security_nama', fn($item) => e($item->security_nama ?? '-'))
            ->editColumn('security_nik', fn($item) => e($item->security_nik ?? '-'))
            ->editColumn('security_dept', fn($item) => e($item->security_dept ?? '-'))

            // 🧑 Pengunjung
            ->editColumn('visitor_nama', fn($item) => e($item->visitor_nama ?? '-'))
            ->editColumn('visitor_company', fn($item) => e($item->visitor_company ?? '-'))
            ->editColumn('visitor_purpose', fn($item) => e($item->visitor_purpose ?? '-'))
            ->editColumn('no_ktp_sim', fn($item) => e($item->no_ktp_sim ?? '-'))
            ->editColumn('nopol', fn($item) => e($item->nopol ?? '-'))
            ->editColumn('plant', fn($item) => e($item->plant ?? '-'))

            // 🕒 Waktu
            ->editColumn('waktu', function ($item) {
                try {
                    $date = Carbon::parse($item->waktu);
                    return $date->format('d-m-Y H:i:s');
                } catch (\Exception $e) {
                    return '-';
                }
            })

            // 🚪 Gate
            ->editColumn('gate', fn($item) => '<span class="badge bg-info">' . e($item->gate) . '</span>')

            // 🔄 Activity Type
            ->editColumn('activity_type', function ($item) {
                $label = $item->activity_type === 'in'
                    ? '<span class="badge bg-primary">Masuk</span>'
                    : '<span class="badge bg-warning text-dark">Keluar</span>';
                return $item->activity_type ? $label : '-';
            })

            // 🖼️ Foto Pengunjung (prioritas: imgvisitorpathin > visitor_foto_url)
            ->addColumn('photo_visitor', function ($item) {
                $url = $item->visitor_foto_url ?? $item->visitor_img_in;


                if (empty($url)) {
                    return '<span class="text-muted">Tidak ada</span>';
                }

                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    return '
                        <img src="' . e($url) . '" 
                             alt="Foto Pengunjung" 
                             style="max-width: 80px; max-height: 80px; border-radius: 6px; cursor: pointer;" 
                             onclick="showImageModal(\'' . e(addslashes($url)) . '\')"
                             class="shadow-sm border">
                    ';
                }

                return '<span class="text-muted">Tidak valid</span>';
            })

            // 📸 Foto dari Gate (opsional)
            ->addColumn('photo_gate', function ($item) {
                $url = $item->foto_dari_gate;
                if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                    return '<span class="text-muted">-</span>';
                }

                return '
                    <img src="' . e($url) . '" 
                         alt="Foto Gate" 
                         style="max-width: 60px; max-height: 60px; border-radius: 4px; cursor: pointer;" 
                         onclick="showImageModal(\'' . e(addslashes($url)) . '\')"
                         class="shadow-sm border">
                ';
            })

            // 🛠 Aksi (opsional: detail modal)
            // ->addColumn('action', function ($item) {
            //     return '
            //         <div class="text-center">
            //             <button class="btn btn-sm btn-soft-info" 
            //                     onclick="openSecurityAccessDetail(' . $item->security_log_id . ')" 
            //                     title="Lihat Detail">
            //                 <i class="ri-eye-fill"></i>
            //             </button>
            //         </div>
            //     ';
            // })

            // 🧩 Tentukan kolom yang boleh HTML
            ->rawColumns([
                'security_nama',
                'security_nik',
                'security_dept',
                'visitor_nama',
                'visitor_company',
                'visitor_purpose',
                'waktu',
                'gate',
                'activity_type',
                'photo_visitor',
                'photo_gate',
                // 'action'
            ])
            ->make(true);
    }
}
