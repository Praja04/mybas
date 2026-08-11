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
            $query = DB::table('ga_visitor_transaction as v')
                ->leftJoin('ga_cek_kendaraan as c', function ($join) {
                    $join->on(DB::raw('CONVERT(c.trnvisitorid USING latin1)'), '=', DB::raw('CONVERT(v.trnvisitorid USING latin1)'))
                        ->whereColumn('c.created_at', '>=', 'v.created_at');
                })
                ->where('v.keterangan', 'SUPIR')
                // Active visits (still inside)
                ->where(function ($q) {
                    $q->whereNull('v.kartu_dikembalikan')
                        ->orWhere('v.kartu_dikembalikan', 0);
                })
                ->whereNull('v.dateout')
                // Vehicle checking checked_in_at and checked_out_at do not exist yet (null)
                ->whereNull('c.checked_in_at')
                ->whereNull('c.checked_out_at')
                ->whereNotNull('v.nopol')
                ->where('v.nopol', '!=', '')
                ->select([
                    'v.trnvisitorid',
                    'v.nopol',
                    'v.namacomp as nama_perusahaan'
                ])
                ->distinct();

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
