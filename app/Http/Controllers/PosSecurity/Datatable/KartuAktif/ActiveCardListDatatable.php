<?php

namespace App\Http\Controllers\PosSecurity\Datatable\KartuAktif;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ActiveCardListDatatable extends Controller
{
    public function index(Request $request)
    {
        $supplierQuery = DB::table('ga_visitor_transaction')
            ->select('trnvisitorid', 'namavisitor', 'namacomp', 'no_kartu', 'datein', 'timein', DB::raw("'supplier' as type_visitor"))
            ->whereNull('dateout')
            ->where('kartu_dikembalikan', false);

        $vendorQuery = DB::table('ga_visitor_vendor')
            ->select('trnvisitorid', 'namavisitor', 'namacomp', 'no_kartu', 'datein', 'timein', DB::raw("'vendor' as type_visitor"))
            ->whereNull('dateout')
            ->where('kartu_dikembalikan', false);

        $query = DB::table(DB::raw("({$supplierQuery->toSql()} UNION {$vendorQuery->toSql()}) as combined_visitors"))
            ->mergeBindings($supplierQuery)
            ->mergeBindings($vendorQuery);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('datein', function($row) {
                return $row->datein . ' ' . $row->timein;
            })
            ->addColumn('action', function($row) {
                return '<button class="btn btn-sm btn-danger btn-reset-kartu" 
                            data-id="'.$row->trnvisitorid.'" 
                            data-type="'.$row->type_visitor.'"
                            data-nama="'.$row->namavisitor.'"
                            data-kartu="'.$row->no_kartu.'">
                            <i class="ri-refresh-line"></i> Reset
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
