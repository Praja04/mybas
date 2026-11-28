<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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
            // Subquery untuk mengambil ID transaksi terbaru untuk setiap plat nomor (nopol)
            // Mengantisipasi jika ada nopol yang sama masuk kembali karena kartu sebelumnya belum terupdate (error keluar)
            $latestPerNopolSub = DB::table('ga_visitor_transaction')
                ->select(DB::raw('MAX(id) as max_id'))
                ->where('keterangan', 'SUPIR')
                ->whereNotNull('nopol')
                ->where('nopol', '!=', '')
                ->where(function ($q) {
                    $q->whereNull('kartu_dikembalikan')
                        ->orWhere('kartu_dikembalikan', 0);
                })
                ->where('created_at', '>=', Carbon::now()->subDays(15)->startOfDay())
                ->groupBy(DB::raw("REPLACE(REPLACE(UPPER(nopol), ' ', ''), '-', '')"));

            // Query utama join dengan subquery untuk mengambil hanya data transaksi paling baru per plat
            $query = DB::table('ga_visitor_transaction as gvt')
                ->joinSub($latestPerNopolSub, 'latest_truck', function ($join) {
                    $join->on('gvt.id', '=', 'latest_truck.max_id');
                })
                ->select([
                    'gvt.trnvisitorid',
                    'gvt.nopol',
                    'gvt.namacomp as nama_perusahaan',
                    'gvt.namavisitor as nama_driver',
                    'gvt.nohpdriver as no_hp_driver',
                    'gvt.created_at',
                    DB::raw("COALESCE(CONCAT(gvt.datein, ' ', gvt.timein), CAST(gvt.created_at AS CHAR)) as checkin_pos1"),
                ]);

            // Search by nopol or company name
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('gvt.nopol', 'like', '%' . $search . '%')
                        ->orWhere('gvt.namacomp', 'like', '%' . $search . '%');
                });
            }

            // Limit results to avoid memory issues
            $limit = min((int) $request->input('limit', 100), 500);
            $query->orderBy('gvt.namacomp', 'asc')
                ->orderBy('gvt.nopol', 'asc')
                ->limit($limit);

            $data = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Data supplier yang belum kembalikan kartu berhasil diambil',
                'count'   => $data->count(),
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier: ' . $e->getMessage(),
            ], 500);
        }
    }
}
