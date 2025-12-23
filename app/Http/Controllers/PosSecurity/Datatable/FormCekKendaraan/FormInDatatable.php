<?php

namespace App\Http\Controllers\PosSecurity\Datatable\FormCekKendaraan;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FormInDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->DrawTable($query);
    }

    private function rawData($request)
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // visitor TRANSACTION
        $transaction = DB::table('ga_visitor_transaction')
            ->select([
                'trnvisitorid',
                'nopol',
                'namavisitor',
                'namacomp',
                DB::raw("'transaction' as source"),
                'created_at',
            ])
            ->where('created_at', '>=', $sevenDaysAgo);

        // visitor VENDOR
        $vendor = DB::table('ga_visitor_vendor')
            ->select([
                'trnvisitorid',
                'nopol',
                'namavisitor',
                'namacomp',
                DB::raw("'vendor' as source"),
                'created_at',
            ])
            ->where('created_at', '>=', $sevenDaysAgo);

        // UNION visitor
        $visitors = DB::query()->fromSub(
            $transaction->unionAll($vendor),
            'v'
        );

        // LEFT JOIN cek kendaraan
        return DB::query()
            ->fromSub($visitors, 'v')
            ->leftJoin('ga_cek_kendaraan as c', function ($join) use ($sevenDaysAgo) {
                $join->on('c.trnvisitorid', '=', 'v.trnvisitorid')
                    ->where('c.created_at', '>=', $sevenDaysAgo);
            })
            ->whereNull('c.trncekid') // tampilkan yang belum cek sama sekali
            ->select([
                'v.trnvisitorid',
                'v.nopol as nomor_polisi',
                'v.namavisitor',
                'v.namacomp',
                'v.source',

                'c.trncekid',
                'c.truck_type',
                'c.muatan_type',
                'c.checked_in_at',
                'c.checked_out_at',
                'c.created_at as cek_created_at',
            ])
            ->orderByRaw('IFNULL(c.created_at, v.created_at) DESC')
            ->limit(300);
    }

    private function DrawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nomor_polisi', function ($item) {
                return '<strong>' . ($item->nomor_polisi ?: '-') . '</strong>';
            })
            ->addColumn('action', function ($item) {
                return '
                    <button 
                        type="button"
                        class="btn btn-sm btn-primary"
                        onclick="openMainForm(
                            \'' . $item->trnvisitorid . '\',
                            \'' . e($item->nomor_polisi) . '\',
                            \'' . e($item->namavisitor) . '\',
                            \'' . e($item->namacomp) . '\'
                        )">
                            Lakukan Cek Masuk
                    </button>
                ';
            })
            ->addColumn('status', function ($item) {
                if (empty($item->checked_in_at)) {
                    return '<span class="badge bg-danger">Belum Cek Masuk</span>';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['nomor_polisi', 'action', 'status'])
            ->make(true);
    }
}
