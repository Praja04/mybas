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
            $query = DB::table('ga_visitor_transaction')
                ->select([
                    'trnvisitorid',
                    'nopol',
                    'namacomp as nama_perusahaan',
                    'namavisitor as nama_driver',
                    'nohpdriver as no_hp_driver',
                    DB::raw("COALESCE(CONCAT(datein, ' ', timein), CAST(created_at AS CHAR)) as checkin_pos1"),
                ])
                ->where('keterangan', 'SUPIR')
                ->whereNotNull('nopol')
                ->where('nopol', '!=', '')
                ->where(function ($q) {
                    $q->whereNull('kartu_dikembalikan')
                        ->orWhere('kartu_dikembalikan', 0);
                });

            // Search by nopol or company name
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nopol', 'like', '%' . $search . '%')
                        ->orWhere('namacomp', 'like', '%' . $search . '%');
                });
            }

            // Limit results to avoid memory issues
            $limit = min((int) $request->input('limit', 100), 500);
            $query->orderBy('namacomp', 'asc')
                ->orderBy('nopol', 'asc')
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
