<?php

namespace App\Http\Controllers\PosSecurity\Datatable\History;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaVisitorTransaction;

class HistorySupplierDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->DrawTable($query);
    }

    private function rawData($request)
    {
        $filter = $request->query('filter', []);

        $query = GaVisitorTransaction::query()
            ->whereIn('purpose', ['MUAT', 'BONGKAR'])
            ->orderBy('createdon', 'desc');



        // 🚫 Exclude blacklist by subquery
        $query->whereNotExists(function ($sub) {
            $sub->select(DB::raw(1))
                ->from('ga_lgtk_blacklist_identitas as bl')
                ->whereColumn('bl.tanggal_lahir', 'ga_visitor_transaction.tgl_lahir')
                ->where(DB::raw('LOWER(TRIM(bl.nama))'), DB::raw('LOWER(TRIM(ga_visitor_transaction.namavisitor))'))
                ->where('bl.aktif', true);
        });

        // Filters...
        if (!empty($filter['nama_visitor'])) {
            $query->where('namavisitor', 'like', '%' . $filter['nama_visitor'] . '%');
        }

        if (!empty($filter['no_ktp_sim'])) {
            $query->where('no_ktp_sim', 'like', '%' . $filter['no_ktp_sim'] . '%');
        }

        if (!empty($filter['no_kartu'])) {
            $query->where('no_kartu', 'like', '%' . $filter['no_kartu'] . '%');
        }

        if (!empty($filter['nopol'])) {
            $query->where('nopol', 'like', '%' . $filter['nopol'] . '%');
        }

        if (!empty($filter['purpose'])) {
            $query->where('purpose', $filter['purpose']);
        }

        if (isset($filter['kartu_dikembalikan']) && $filter['kartu_dikembalikan'] !== '') {
            $query->where('kartu_dikembalikan', (bool) $filter['kartu_dikembalikan']);
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            try {
                $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
                $end = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->endOfDay();
                $query->whereBetween('createdon', [$start, $end]);
            } catch (\Exception $e) {
                // Log error if needed
            }
        }


        // ⏳ Batasi ke 7 hari terakhir
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $query->where('createdon', '>=', $sevenDaysAgo);

        // $query->orderBy('id', 'desc');
        $query->orderBy('createdon', 'desc');


        return $query->limit(300)->get();
    }

    private function DrawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('waktu_masuk', function ($item) {
                if (!$item->datein && !$item->timein) return '-';

                $tanggal = $item->datein ? date('d-m-Y', strtotime($item->datein)) : '-';
                $jam = $item->timein ? date('H:i', strtotime($item->timein)) : '-';

                return '<span class="d-block mb-1">📅 <strong>' . $tanggal . '</strong></span>' .
                    '<span>⏰ <strong>' . $jam . '</strong></span>';
            })
            ->addColumn('waktu_keluar', function ($item) {
                if (!$item->dateout && !$item->timeout) return '-';

                $tanggal = $item->dateout ? date('d-m-Y', strtotime($item->dateout)) : '-';
                $jam = $item->timeout ? date('H:i', strtotime($item->timeout)) : '-';

                return '<span class="d-block mb-1">📅 <strong>' . $tanggal . '</strong></span>' .
                    '<span>⏰ <strong>' . $jam . '</strong></span>';
            })
            ->editColumn('namavisitor', function ($item) {
                $namaVisitor = $item->namavisitor ?: '-';

                return '<div style="max-width: 100px; word-wrap: break-word; white-space: normal;">' . e($namaVisitor) . '</div>';
            })


            ->editColumn('namacomp', function ($item) {
                $namaComp = $item->namacomp ?: '-';

                return '<div style="max-width: 100px; word-wrap: break-word; white-space: normal;">' . e($namaComp) . '</div>';
            })

            ->addColumn('photo_visitor', function ($item) {
                if (empty($item->foto)) {
                    return '-';
                }

                // Decode JSON string jadi array
                $fotoArray = json_decode(html_entity_decode($item->foto), true);

                if (empty($fotoArray) || !is_array($fotoArray)) {
                    return '-';
                }

                $html = '';

                foreach ($fotoArray as $fotoUrl) {
                    $html .= '<img src="' . $fotoUrl . '" 
                      alt="Photo Visitor" 
                      style="max-width: 80px; max-height: 80px; margin: 3px; border-radius: 6px; cursor: pointer;" 
                      onclick="showImageModal(\'' . $fotoUrl . '\')" />';
                }

                return $html;
            })
            ->addColumn('img_visitor', function ($item) {
                if (empty($item->imgvisitorpathin)) {
                    return '-';
                }
                return '<img src="' . $item->imgvisitorpathin . '" 
             alt="Image Visitor" 
             style="max-width: 80px; max-height: 80px; border-radius: 6px; cursor: pointer; margin: 3px;" 
             onclick="showImageModal(\'' . $item->imgvisitorpathin . '\')" />';
            })
            ->editColumn('NAMAVISITOR', function ($item) {
                return $item->NAMAVISITOR ?: '-';
            })
            ->editColumn('NAMACOMP', function ($item) {
                return $item->NAMACOMP ?: '-';
            })
            ->editColumn('PURPOSE', function ($item) {
                return $item->PURPOSE ?: '-';
            })
            ->editColumn('GATEIDIN', function ($item) {
                return $item->GATEIDIN ?: '-';
            })
            ->editColumn('GATELINEIDIN', function ($item) {
                return $item->GATELINEIDIN ?: '-';
            })
            ->editColumn('TIMEIN', function ($item) {
                return $item->TIMEIN ? date('d-m-Y H:i', strtotime($item->TIMEIN)) : '-';
            })
            ->editColumn('DATEIN', function ($item) {
                return $item->DATEIN ? date('d-m-Y', strtotime($item->DATEIN)) : '-';
            })
            ->editColumn('NOHPDRIVER', function ($item) {
                return $item->NOHPDRIVER ?: '-';
            })
            ->editColumn('KETERANGAN', function ($item) {
                return $item->KETERANGAN ?: '-';
            })
            ->addColumn('action', function ($item) {
                // return '
                // <div class="dropdown d-inline-block">
                //     <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                //         <i class="ri-more-fill align-middle"></i>
                //     </button>
                //     <ul class="dropdown-menu dropdown-menu-end">
                //         <li>
                //             <a href="#!" class="dropdown-item" onclick="openVisitorActionModal(\'' . $item->trnvisitorid . '\')">
                //                 <i class="ri-eye-fill align-bottom me-2 text-muted"></i>View Detail
                //             </a>
                //         </li>
                //         <li>
                //             <a href="#!" class="dropdown-item text-danger" onclick="triggerBlacklistVisitor(\'' . $item->trnvisitorid . '\')">
                //                 <i class="ri-close-circle-fill align-bottom me-2 text-muted"></i>Block Visitor
                //             </a>
                //         </li>
                //         <li>
                //             <a href="#!" class="dropdown-item text-warning" onclick="triggerReportLostCard(\'' . $item->trnvisitorid . '\')">
                //                 <i class="ri-error-warning-fill align-bottom me-2 text-muted"></i>Report Kartu Hilang
                //             </a>
                //         </li>
                //     </ul>
                // </div>';

                // test
                // return '
                // <div class="dropdown d-inline-block">
                //     <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                //         <i class="ri-more-fill align-middle"></i>
                //     </button>
                //     <ul class="dropdown-menu dropdown-menu-end">

                //     </ul>
                // </div>';
                return '
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="#!" class="dropdown-item" onclick="openVisitorActionModal(\'' . $item->trnvisitorid . '\')">
                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i>View Detail
                                </a>
                            </li>
                            </ul>
                            </div>
                            ';
                // <li>
                //     <a href="#!" class="dropdown-item text-danger" onclick="triggerBlacklistVisitor(\'' . $item->trnvisitorid . '\')">
                //         <i class="ri-close-circle-fill align-bottom me-2 text-muted"></i>Block Visitor
                //     </a>
                // </li>
            })


            ->rawColumns(['photo_visitor', 'img_visitor', 'namacomp', 'waktu_masuk', 'waktu_keluar', 'namavisitor', 'action', 'keterangan'])
            ->make(true);
    }
}
