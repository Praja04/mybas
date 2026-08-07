<?php

namespace App\Http\Controllers\PosSecurity\Datatable\FormCekKendaraan;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FormOutDatatable extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = $this->rawData($request);

        if (!empty($search)) {
            $query->where('v.nopol', 'like', '%' . $search . '%');
        }

        $paginated = $query->paginate($perPage);

        $paginated->getCollection()->transform(function ($item, $key) use ($paginated) {
            $item->DT_RowIndex = ($paginated->currentPage() - 1) * $paginated->perPage() + $key + 1;
            
            $item->nomor_polisi_html = '<strong>' . ($item->nomor_polisi ?: '-') . '</strong>';
            $item->action_html = '
                <button 
                    type="button"
                    class="btn btn-sm btn-primary open-form-out"
                    data-trncekid="' . e($item->trncekid) . '"
                    data-trnvisitorid="' . e($item->trnvisitorid) . '"
                    data-nomor-polisi="' . e($item->nomor_polisi) . '"
                    data-nama-supir="' . e($item->namavisitor) . '"
                    data-company="' . e($item->namacomp) . '"
                    data-muatan-type="' . e($item->muatan_type) . '"
                    data-truck-type="' . e($item->truck_type) . '"
                    data-truck-type-other="' . e($item->truck_type_other) . '"
                    data-checked-in-at="' . e($item->checked_in_at) . '"
                >
                    Lakukan Cek Keluar
                </button>
            ';
            $item->status_html = '<span class="badge bg-warning">Belum Cek Keluar</span>';
            
            return $item;
        });

        return response()->json($paginated);
    }

    private function rawData($request)
    {
        // visitor TRANSACTION
        $transaction = DB::table('ga_visitor_transaction')
            ->select([
                'trnvisitorid',
                'nopol',
                'namavisitor',
                'namacomp',
                'kartu_dikembalikan',
                DB::raw("'transaction' as source"),
                'created_at',
            ])
            ->where('keterangan', 'SUPIR');

        // visitor VENDOR
        $vendor = DB::table('ga_visitor_vendor')
            ->select([
                'trnvisitorid',
                'nopol',
                'namavisitor',
                'namacomp',
                'kartu_dikembalikan',
                DB::raw("'vendor' as source"),
                'created_at',
            ])
            ->whereNotNull('nopol')
            ->where('nopol', '!=', '')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ga_visitor_transaction')
                    ->where('ga_visitor_transaction.keterangan', 'SUPIR')
                    ->whereRaw("
                        CONVERT(REPLACE(REPLACE(UPPER(ga_visitor_transaction.nopol), ' ', ''), '-', '') USING latin1)
                        =
                        CONVERT(REPLACE(REPLACE(UPPER(ga_visitor_vendor.nopol), ' ', ''), '-', '') USING latin1)
                    ");
            });

        // UNION visitor
        $visitors = DB::query()->fromSub(
            $transaction->unionAll($vendor),
            'v'
        );

        // LEFT JOIN cek kendaraan
        return DB::query()
            ->fromSub($visitors, 'v')
            ->leftJoin('ga_cek_kendaraan as c', function ($join) {
                $join->on(DB::raw('CONVERT(c.trnvisitorid USING latin1)'), '=', DB::raw('CONVERT(v.trnvisitorid USING latin1)'))
                    ->whereColumn('c.created_at', '>=', 'v.created_at');
            })
            ->whereNotNull('c.checked_in_at') // sudah cek masuk
            ->whereNull('c.checked_out_at') // tapi belum cek keluar
            ->where(function ($q) {
                $q->whereNull('v.kartu_dikembalikan')
                    ->orWhere('v.kartu_dikembalikan', 0);
            })

            ->select([
                'v.trnvisitorid',
                'v.nopol as nomor_polisi',
                'v.namavisitor',
                'v.namacomp',
                'v.source',

                'c.trncekid',
                'c.truck_type',
                'c.muatan_type',
                'c.truck_type_other',
                'c.checked_in_at',
                'c.checked_out_at',
                'c.created_at as cek_created_at',
            ])
            ->orderBy('c.checked_in_at', 'DESC')
            ->limit(300);
    }

}
