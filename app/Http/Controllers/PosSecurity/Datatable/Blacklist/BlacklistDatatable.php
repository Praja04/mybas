<?php

namespace App\Http\Controllers\PosSecurity\Datatable\Blacklist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Models\PosSecurity\Logging\BlacklistIdentitas;

class BlacklistDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->DrawTable($query);
    }

    private function rawData($request)
    {
        $filter = $request->query('filter', []);

        $query = BlacklistIdentitas::query()
            ->orderByDesc('tanggal_blacklist');

        if (!empty($filter['nama'])) {
            $query->where('nama', 'like', '%' . $filter['nama'] . '%');
        }

        if (!empty($filter['no_identitas'])) {
            $query->where('no_identitas', 'like', '%' . $filter['no_identitas'] . '%');
        }

        if (!empty($filter['jenis_identitas'])) {
            $query->where('jenis_identitas', $filter['jenis_identitas']);
        }

        if (!empty($filter['alasan_blacklist'])) {
            $query->where('alasan_blacklist', 'like', '%' . $filter['alasan_blacklist'] . '%');
        }

        if (isset($filter['aktif']) && $filter['aktif'] !== '') {
            $query->where('aktif', (bool) $filter['aktif']);
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            try {
                $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
                $end = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->endOfDay();
                $query->whereBetween('tanggal_blacklist', [$start, $end]);
            } catch (\Exception $e) {
                // Optional: log error
            }
        }

        return $query->limit(200)->get();
    }

    private function DrawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama', fn($item) => e($item->nama ?: '-'))
            ->editColumn('no_identitas', fn($item) => e($item->no_identitas ?: '-'))
            ->editColumn('jenis_identitas', fn($item) => e($item->jenis_identitas ?: '-'))
            ->editColumn('tanggal_lahir', fn($item) => $item->tanggal_lahir ? $item->tanggal_lahir->format('d-m-Y') : '-')
            ->editColumn('tanggal_blacklist', fn($item) => $item->tanggal_blacklist ? $item->tanggal_blacklist->format('d-m-Y H:i') : '-')
            ->editColumn('alasan_blacklist', fn($item) => e($item->alasan_blacklist ?: '-'))
            ->editColumn('diblacklist_oleh', fn($item) => e($item->diblacklist_oleh ?: '-'))
            ->editColumn('aktif', fn($item) => $item->aktif ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>')
            ->addColumn('action', function ($item) {
                $cancelOption = '';
                if ($item->aktif) {
                    $cancelOption = '
              <li>
                <a href="#!" class="dropdown-item text-danger" onclick="cancelBlacklist(' . $item->id . ')">
                  <i class="ri-close-circle-fill align-bottom me-2"></i> Batalkan Blacklist
                </a>
              </li>';
                }
                return '
          <div class="dropdown d-inline-block">
            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ri-more-fill align-middle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a href="#!" class="dropdown-item" onclick="openBlacklistDetailModal(' . $item->id . ')">
                  <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Lihat Detail
                </a>
              </li>' . $cancelOption . '
            </ul>
          </div>';
            })
            ->rawColumns(['aktif', 'action'])
            ->make(true);
    }
}
