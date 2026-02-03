<?php

namespace App\Http\Controllers\PosSecurity\Datatable\History;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaCekKendaraan;

class HistoryCekKendaraanDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->DrawTable($query);
    }

    // hanya tampil yang sudah pernah cek
    // private function rawData($request)
    // {
    //     $query = GaCekKendaraan::query();

    //     // Batasi ke 7 hari terakhir
    //     $sevenDaysAgo = Carbon::now()->subDays(7);
    //     $query->where('created_at', '>=', $sevenDaysAgo);

    //     $query->orderBy('created_at', 'desc');

    //     return $query->limit(300)->get();
    // }

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
                'kartu_dikembalikan',
                DB::raw("'transaction' as source"),
                'created_at',
            ])
            ->where('keterangan', 'SUPIR')
            ->where('created_at', '>=', $sevenDaysAgo);

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
            ->where('created_at', '>=', $sevenDaysAgo)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ga_visitor_transaction')
                    ->where('ga_visitor_transaction.keterangan', 'SUPIR')
                    ->whereRaw("
                REPLACE(REPLACE(UPPER(ga_visitor_transaction.nopol), ' ', ''), '-', '')
                =
                REPLACE(REPLACE(UPPER(ga_visitor_vendor.nopol), ' ', ''), '-', '')
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
            // ->leftJoin('ga_cek_kendaraan as c', function ($join) use ($sevenDaysAgo) {
            //     $join->on('c.trnvisitorid', '=', 'v.trnvisitorid')
            //         ->where('c.created_at', '>=', $sevenDaysAgo);
            // })
            ->leftJoin('ga_cek_kendaraan as c', function ($join) {
                $join->on('c.trnvisitorid', '=', 'v.trnvisitorid')
                    ->whereColumn('c.created_at', '>=', 'v.created_at');
            })
            ->select([
                'v.trnvisitorid',
                'v.nopol as nomor_polisi',
                'v.namavisitor',
                'v.namacomp',
                'v.source',
                'v.kartu_dikembalikan',

                'c.trncekid',
                'c.truck_type',
                'c.muatan_type',
                'c.checked_in_at',
                'c.checked_out_at',
                'c.created_at as cek_created_at',
            ])
            ->orderByRaw('IFNULL(c.created_at, v.created_at) DESC')
            ->limit(300);

        // dd([
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings(),
        // ]);

        // return $query;
    }

    private function DrawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('jenis', function ($item) {
                if (!$item->trncekid) {
                    return '-';
                }

                $truck  = $item->truck_type ?: '-';
                $muatan = $item->muatan_type ?: '-';

                // jika jenis truk "LAINNYA", jangan tampilkan muatan
                if (stripos($truck, 'LAINNYA') !== false) {
                    return "<span>{$truck}</span>";
                }

                return "<span>{$truck}</span><br><span>({$muatan})</span>";
            })
            ->editColumn('nomor_polisi', function ($item) {
                return '<strong>' . ($item->nomor_polisi ?: '-') . '</strong>';
            })
            // ->addColumn('action', function ($item) {
            //     return '
            //         <a href="#" class="dropdown-item" onclick="openCekKendaraanActionModal(\'' . $item->trncekid . '\')">
            //             <i class="ri-eye-fill align-bottom me-2 text-muted"></i>Lihat Detail
            //         </a>
            //     ';
            // })
            ->addColumn('action', function ($item) {
                if ($item->trncekid) {
                    return '
                        <a href="#" class="dropdown-item"
                            onclick="openCekKendaraanActionModal(\'' . $item->trncekid . '\')">
                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i>Lihat Detail
                            </a>
                        ';
                }

                return '-';
            })
            ->addColumn('waktu', function ($item) {
                if (!$item->checked_in_at) {
                    return '-';
                }

                $in  = Carbon::parse($item->checked_in_at);
                $out = $item->checked_out_at
                    ? Carbon::parse($item->checked_out_at)
                    : null;

                $inText = $in->translatedFormat('d M Y H:i');
                $outText = $out
                    ? $out->translatedFormat('d M Y H:i')
                    : '-';

                return "
                    <div>
                        <div><strong>Masuk :</strong> {$inText}</div>
                        <div><strong>Keluar :</strong> {$outText}</div>
                    </div>
                ";
            })
            ->addColumn('durasi', function ($item) {
                if (!$item->checked_in_at || !$item->checked_out_at) {
                    return '-';
                }

                $checkIn  = Carbon::parse($item->checked_in_at);
                $checkOut = Carbon::parse($item->checked_out_at);

                $diffMinutes = $checkIn->diffInMinutes($checkOut);

                $hours   = intdiv($diffMinutes, 60);
                $minutes = $diffMinutes % 60;

                $result = [];

                if ($hours > 0) {
                    $result[] = $hours . ' jam';
                }

                if ($minutes > 0) {
                    $result[] = $minutes . ' menit';
                }

                return implode(' ', $result) ?: '0 menit';
            })
            ->addColumn('status', function ($item) {
                // 1. Tidak dilakukan cek kendaraan (historis)
                if (
                    $item->kartu_dikembalikan == 1 &&
                    !$item->trncekid
                ) {
                    return '<span class="badge bg-danger">
                        Tidak Dilakukan Cek Kendaraan
                    </span>';
                }

                // 2. Tidak cek keluar
                if (
                    $item->kartu_dikembalikan == 1 &&
                    !empty($item->checked_in_at) && empty($item->checked_out_at)
                ) {
                    return '<span class="badge bg-danger">
                        Tidak Dilakukan Cek Keluar
                    </span>';
                }

                // 2. Belum cek kendaraan
                if (empty($item->checked_in_at)) {
                    return '<span class="badge bg-danger">Belum Cek Kendaraan</span>';
                }

                // 3. Sudah cek tapi belum keluar
                if (!empty($item->checked_in_at) && empty($item->checked_out_at)) {
                    return '<span class="badge bg-warning">Belum Cek Keluar</span>';
                }

                // 4. Sudah keluar (lengkap)
                return '<span class="badge bg-success">Sudah Cek Keluar</span>';
            })
            ->rawColumns(['nomor_polisi', 'jenis', 'action', 'durasi', 'waktu', 'status'])
            ->make(true);
    }
    // <div class="dropdown d-inline-block">
    //     <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    //         <i class="ri-more-fill align-middle"></i>
    //     </button>
    //     <ul class="dropdown-menu dropdown-menu-end">
    //         <li>
    //             <a href="#!" class="dropdown-item" onclick="openCekKendaraanActionModal(\'' . $item->trncekid . '\')">
    //                 <i class="ri-eye-fill align-bottom me-2 text-muted"></i>View Detail
    //             </a>
    //         </li>
    //     </ul>
    // </div>
}
