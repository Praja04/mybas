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
        $filter = $request->input('filter', []);

        $query = GaVisitorTransaction::query()
            ->whereIn('keterangan', ['SUPIR', 'KERNET']);

        // Filter tanggal masuk (datein)
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            // RANGE tanggal
            $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->toDateString();
            $end   = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->toDateString();
            $query->whereBetween('datein', [$start, $end]);
        } elseif (!empty($filter['start_date'])) {
            // SATU tanggal saja
            $date = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->toDateString();
            $query->where('datein', $date);
        }

        $query->orderBy('datein', 'desc')
            ->orderBy('timein', 'desc');

        return $query;
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
            ->addColumn('action', function ($item) {
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
                            <li>
                                <a href="#!" class="dropdown-item text-danger" onclick="triggerBlacklistVisitor(\'' . $item->trnvisitorid . '\')">
                                    <i class="ri-close-circle-fill align-bottom me-2 text-danger"></i>Blacklist
                                </a>
                            </li>
                            <li>
                                <a href="#!" class="dropdown-item text-warning" onclick="triggerReportLostCard(\'' . $item->trnvisitorid . '\')">
                                    <i class="ri-alert-fill align-bottom me-2 text-warning"></i>Lapor Kartu Hilang
                                </a>
                            </li>
                        </ul>
                    </div>';
            })
            ->rawColumns(['namacomp', 'waktu_masuk', 'waktu_keluar', 'namavisitor', 'action', 'keterangan'])
            ->make(true);
    }
}
