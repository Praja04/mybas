<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierApiController extends Controller
{
    /**
     * Get unique supplier license plates (nopol) and company names.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupplierData(Request $request)
    {
        try {
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
                    'nohpdriver',
                ])
                ->where('keterangan', 'SUPIR')
                ->where(function ($q) {
                    $q->whereNull('kartu_dikembalikan')
                      ->orWhere('kartu_dikembalikan', 0);
                });

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
                    'nohpdriver',
                ])
                ->whereNotNull('nopol')
                ->where('nopol', '!=', '')
                ->where(function ($q) {
                    $q->whereNull('kartu_dikembalikan')
                      ->orWhere('kartu_dikembalikan', 0);
                })
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
            $query = DB::query()
                ->fromSub($visitors, 'v')
                ->leftJoin('ga_cek_kendaraan as c', function ($join) {
                    $join->on(DB::raw('CONVERT(c.trnvisitorid USING latin1)'), '=', DB::raw('CONVERT(v.trnvisitorid USING latin1)'))
                        ->whereColumn('c.created_at', '>=', 'v.created_at');
                })
                ->whereNull('c.trncekid') // tampilkan yang belum cek sama sekali
                ->select([
                    'v.trnvisitorid',
                    'v.nopol',
                    'v.namacomp as nama_perusahaan',
                    'v.namavisitor as nama_driver',
                    'v.nohpdriver as no_hp_driver'
                ]);

            // Search by nopol or company name
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('v.nopol', 'like', '%' . $search . '%')
                        ->orWhere('v.namacomp', 'like', '%' . $search . '%');
                });
            }

            // Limit results to avoid memory issues
            $limit = min((int) $request->input('limit', 100), 500);

            $query->orderBy('v.namacomp', 'asc')
                ->orderBy('v.nopol', 'asc')
                ->limit($limit);

            $data = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Data supplier yang belum pengecekan kendaraan berhasil diambil',
                'count'   => $data->count(),
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier: ' . $e->getMessage()
            ], 500);
        }
    }
}
