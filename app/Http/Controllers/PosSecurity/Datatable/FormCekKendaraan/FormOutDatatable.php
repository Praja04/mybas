<?php

namespace App\Http\Controllers\PosSecurity\Datatable\FormCekKendaraan;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        // Ambil data transaksi kendaraan dari project_warehouse via HTTP API
        $warehouseData = [];
        $nopols = $paginated->getCollection()->map(function ($i) {
            return strtoupper(str_replace([' ', '-'], '', $i->nomor_polisi));
        })->filter()->unique()->values()->all();

        if (!empty($nopols)) {
            try {
                $rawUrl = rtrim(config('services.warehouse.api_url', 'http://127.0.0.1:8000'), '/');
                $baseUrl = str_ends_with($rawUrl, '/api') ? $rawUrl : "{$rawUrl}/api";
                $timeout = (float) config('services.warehouse.timeout', 2.0);

                $response = Http::timeout($timeout)->post("{$baseUrl}/vehicle/transactions/batch", [
                    'nopols' => $nopols,
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    if (!empty($resJson['data'])) {
                        foreach ($resJson['data'] as $cleanKey => $itemData) {
                            $warehouseData[$cleanKey] = (object) [
                                'target_area_name'    => $itemData['target_area'] ?? null,
                                'target_area_code'    => $itemData['target_area_code'] ?? null,
                                'no_antrian'          => $itemData['no_antrian'] ?? null,
                                'queue_taken_time'    => $itemData['queue_taken_time'] ?? null,
                                'unloading_status'    => $itemData['unloading_status'] ?? null,
                                'warehouse_status'    => $itemData['status'] ?? null,
                                'finish_loading_time' => $itemData['finish_loading_time'] ?? null,
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fallback gracefully jika koneksi warehouse API terkendala atau offline
                Log::warning("Gagal mengambil data transaksi kendaraan dari Warehouse API (FormOut): " . $e->getMessage());
            }
        }

        $paginated->getCollection()->transform(function ($item, $key) use ($paginated, $warehouseData) {
            $item->DT_RowIndex = ($paginated->currentPage() - 1) * $paginated->perPage() + $key + 1;

            $cleanKey = strtoupper(str_replace([' ', '-'], '', $item->nomor_polisi));
            $wh = $warehouseData[$cleanKey] ?? null;

            $areaTujuan = $wh && $wh->target_area_name ? $wh->target_area_name : '-';
            $noAntrian = $wh && $wh->no_antrian ? $wh->no_antrian : null;
            $unloadingStatus = $wh && $wh->unloading_status ? $wh->unloading_status : 'pending';
            $warehouseStatus = $wh && $wh->warehouse_status ? $wh->warehouse_status : null;
            $finishLoadingTime = $wh && $wh->finish_loading_time ? $wh->finish_loading_time : null;

            $isCompleted = ($unloadingStatus === 'completed' || $warehouseStatus === 'timbangan_out');
            $isProcess = ($unloadingStatus === 'process' || in_array($warehouseStatus, ['loading', 'unloading']));

            $item->area_tujuan = $areaTujuan;
            $item->no_antrian = $noAntrian;
            $item->unloading_status = $unloadingStatus;
            $item->warehouse_status = $warehouseStatus;
            $item->finish_loading_time = $finishLoadingTime;

            // Badges informatif untuk kolom tabel
            $parkirBadge = ($item->lokasi_parkir && $item->lokasi_parkir !== '-')
                ? '<span class="badge bg-soft-info text-info"><i class="mdi mdi-parking me-1"></i>' . e($item->lokasi_parkir) . '</span>'
                : '<span class="badge bg-soft-secondary text-muted"><i class="mdi mdi-parking me-1"></i>Belum Parkir</span>';

            $areaBadge = ($areaTujuan && $areaTujuan !== '-')
                ? '<span class="badge bg-soft-primary text-primary"><i class="mdi mdi-warehouse me-1"></i>' . e($areaTujuan) . '</span>'
                : '<span class="badge bg-soft-secondary text-muted"><i class="mdi mdi-warehouse me-1"></i>Area -</span>';

            if ($isCompleted) {
                $antrianBadge = '<span class="badge bg-success text-white"><i class="mdi mdi-check-circle-outline me-1"></i>Selesai Bongkar / Muat</span>';
            } else if ($isProcess) {
                $antrianBadge = '<span class="badge bg-info text-white"><i class="mdi mdi-progress-clock me-1"></i>Sedang Bongkar / Muat' . (!empty($noAntrian) ? ' (No. ' . e($noAntrian) . ')' : '') . '</span>';
            } else if (!empty($noAntrian)) {
                $antrianBadge = '<span class="badge bg-primary text-white"><i class="mdi mdi-ticket me-1"></i>Antrian ' . e($noAntrian) . ' (' . e(ucfirst($unloadingStatus)) . ')</span>';
            } else {
                $antrianBadge = '<span class="badge bg-soft-warning text-warning border border-warning-subtle"><i class="mdi mdi-clock-outline me-1"></i>Belum Antri</span>';
            }

            $item->nomor_polisi_html = '
                <div class="d-flex flex-column gap-1">
                    <span class="fs-14 fw-bold font-monospace text-dark">' . ($item->nomor_polisi ?: '-') . '</span>
                    <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                        ' . $parkirBadge . '
                        ' . $areaBadge . '
                        ' . $antrianBadge . '
                    </div>
                </div>
            ';

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
                    data-lokasi-parkir="' . e($item->lokasi_parkir ?: '-') . '"
                    data-area-tujuan="' . e($areaTujuan) . '"
                    data-no-antrian="' . e($noAntrian ?: '') . '"
                    data-unloading-status="' . e($unloadingStatus) . '"
                    data-warehouse-status="' . e($warehouseStatus ?: '') . '"
                    data-finish-loading-time="' . e($finishLoadingTime ?: '') . '"
                >
                    Lakukan Cek Keluar
                </button>
            ';

            if ($isCompleted) {
                $item->status_html = '
                    <div class="d-flex flex-column gap-1">
                        <span class="badge bg-info text-white"><i class="mdi mdi-scale-balance me-1"></i>Ke Timbangan Out</span>
                        <span class="badge bg-soft-warning text-dark">Belum Cek Keluar</span>
                    </div>
                ';
            } else if ($isProcess) {
                $item->status_html = '
                    <div class="d-flex flex-column gap-1">
                        <span class="badge bg-primary text-white"><i class="mdi mdi-progress-clock me-1"></i>Bongkar / Muat</span>
                        <span class="badge bg-soft-warning text-dark">Belum Cek Keluar</span>
                    </div>
                ';
            } else {
                $item->status_html = '<span class="badge bg-warning">Belum Cek Keluar</span>';
            }

            return $item;
        });

        return response()->json($paginated);
    }

    private function rawData($request)
    {
        // visitor TRANSACTION
        $transaction = DB::table('ga_visitor_transaction')
            ->select([
                DB::raw('CAST(trnvisitorid AS CHAR) COLLATE utf8mb4_unicode_ci as trnvisitorid'),
                DB::raw('CAST(nopol AS CHAR) COLLATE utf8mb4_unicode_ci as nopol'),
                DB::raw('CAST(namavisitor AS CHAR) COLLATE utf8mb4_unicode_ci as namavisitor'),
                DB::raw('CAST(namacomp AS CHAR) COLLATE utf8mb4_unicode_ci as namacomp'),
                'kartu_dikembalikan',
                DB::raw("'transaction' as source"),
                'created_at',
            ])
            ->where('keterangan', 'SUPIR')
            ->where(function ($q) {
                $q->whereNull('kartu_dikembalikan')
                    ->orWhere('kartu_dikembalikan', 0);
            });

        // visitor VENDOR
        $vendor = DB::table('ga_visitor_vendor')
            ->select([
                DB::raw('CAST(trnvisitorid AS CHAR) COLLATE utf8mb4_unicode_ci as trnvisitorid'),
                DB::raw('CAST(nopol AS CHAR) COLLATE utf8mb4_unicode_ci as nopol'),
                DB::raw('CAST(namavisitor AS CHAR) COLLATE utf8mb4_unicode_ci as namavisitor'),
                DB::raw('CAST(namacomp AS CHAR) COLLATE utf8mb4_unicode_ci as namacomp'),
                'kartu_dikembalikan',
                DB::raw("'vendor' as source"),
                'created_at',
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

        // LEFT JOIN cek kendaraan & LEFT JOIN kantong parkir
        return DB::query()
            ->fromSub($visitors, 'v')
            ->leftJoin('ga_cek_kendaraan as c', function ($join) {
                $join->on('c.trnvisitorid', '=', 'v.trnvisitorid')
                    ->whereColumn('c.created_at', '>=', 'v.created_at');
            })
            ->leftJoin('parking_assignments as pa', function ($join) {
                $join->on(function ($q) {
                    $q->whereRaw("pa.catatan LIKE CONCAT('%', v.trnvisitorid, '%')")
                        ->orWhere(function ($subQ) {
                            $subQ->whereRaw("REPLACE(REPLACE(UPPER(pa.no_polisi), ' ', ''), '-', '') = REPLACE(REPLACE(UPPER(v.nopol), ' ', ''), '-', '')")
                                ->whereColumn('pa.created_at', '>=', 'v.created_at');
                        });
                })
                    ->whereIn('pa.status_assignment', ['assigned', 'parked', 'completed'])
                    ->whereNull('pa.deleted_at');
            })
            ->leftJoin('parking_slots as ps', 'ps.id', '=', 'pa.parking_slot_id')
            ->leftJoin('parking_zones as pz', 'pz.id', '=', 'pa.parking_zone_id')
            ->whereNotNull('c.checked_in_at') // sudah cek masuk
            ->whereNull('c.checked_out_at') // tapi belum cek keluar
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

                DB::raw("CASE WHEN ps.kode_slot IS NOT NULL THEN CONCAT(pz.nama_zona, ' - ', ps.kode_slot) ELSE '-' END as lokasi_parkir"),
            ])
            ->orderBy('c.checked_in_at', 'DESC')
            ->limit(300);
    }
}
