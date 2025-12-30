<?php

namespace App\Http\Controllers\PosSecurity\Datatable\History;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaVisitorVendorTransaction;

class HistoryVendorDatatable extends Controller
{
    public function index(Request $request)
    {
        $query = $this->rawData($request);
        return $this->DrawTable($query);
    }

    private function rawData($request)
    {
        $filter = $request->query('filter', []);

        $query = GaVisitorVendorTransaction::query()
            ->orderBy('createdon', 'desc');


        // Filter nama visitor
        if (!empty($filter['nama_visitor'])) {
            $query->where('namavisitor', 'like', '%' . $filter['nama_visitor'] . '%');
        }


        // Filter no_kartu
        if (!empty($filter['no_kartu'])) {
            $query->where('no_kartu', 'like', '%' . $filter['no_kartu'] . '%');
        }

        // Filter type
        if (!empty($filter['type'])) {
            $query->where('type',  $filter['type']);
        }

        // Filter kartu_dikembalikan
        if (isset($filter['kartu_dikembalikan']) && $filter['kartu_dikembalikan'] !== '') {
            $query->where('kartu_dikembalikan', (bool) $filter['kartu_dikembalikan']);
        }

        // Filter tanggal
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            try {
                $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
                $end = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->endOfDay();
                $query->whereBetween('createdon', [$start, $end]);
            } catch (\Exception $e) {
                // Bisa log error format tanggal jika perlu
            }
        }

        // Batasi hanya 7 hari terakhir (optional safeguard)
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $query->where('createdon', '>=', $sevenDaysAgo);

        return $query->limit(300)->get();
    }

    private function DrawTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('waktu_masuk', function ($item) {
                if (!$item->datein && !$item->timein) return '-';

                $tanggal = $item->datein ? date('d-m-Y', strtotime($item->datein)) : '-';
                $jam = $item->timein ? date('H:i', strtotime($item->timein)) : '-';

                return '<span class="d-block mb-1">📅 <strong>' . $tanggal . '</strong></span>' .
                    '<span>⏰ <strong>' . $jam . '</strong></span>';
            })
            ->addColumn('waktu_keluar', function ($item) {
                if (!$item->dateout && !$item->timeout) return '-';

                $tanggal = $item->dateout ? date('d-m-Y', strtotime($item->dateout)) : '-';
                $jam = $item->timeout ? date('H:i', strtotime($item->timeout)) : '-';

                return '<span class="d-block mb-1">📅 <strong>' . $tanggal . '</strong></span>' .
                    '<span>⏰ <strong>' . $jam . '</strong></span>';
            })
            ->editColumn('namavisitor', function ($item) {
                $namaVisitor = $item->namavisitor ?: '-';
                return '<div style="max-width: 100px; word-wrap: break-word; white-space: normal;">' . e($namaVisitor) . '</div>';
            })
            ->editColumn('namacomp', function ($item) {
                $namaComp = $item->namacomp ?: '-';
                return '<div style="max-width: 100px; word-wrap: break-word; white-space: normal;">' . e($namaComp) . '</div>';
            })
            ->addColumn('photo_visitor', function ($item) {
                if (empty($item->foto)) return '-';
                $fotoArray = json_decode(html_entity_decode($item->foto), true);
                if (empty($fotoArray) || !is_array($fotoArray)) return '-';
                $html = '';
                foreach ($fotoArray as $fotoUrl) {
                    $html .= '<img src="' . $fotoUrl . '" style="max-width: 80px; max-height: 80px; margin: 3px; border-radius: 6px; cursor: pointer;" onclick="showImageModal(\'' . $fotoUrl . '\')" />';
                }
                return $html;
            })
            ->addColumn('img_visitor', function ($item) {
                if (empty($item->imgvisitorpathin)) return '-';
                return '<img src="' . $item->imgvisitorpathin . '" style="max-width: 80px; max-height: 80px; border-radius: 6px; cursor: pointer; margin: 3px;" onclick="showImageModal(\'' . $item->imgvisitorpathin . '\')" />';
            })
            ->addColumn('qr_image', function ($item) {
                if (empty($item->imgvisitorpathin)) return '-';
                $qrCodeContent = $item->trnvisitorid;
                $containerId = 'qr-container-' . $item->trnvisitorid;
                $filename = addslashes($qrCodeContent . '.png');
                return <<<HTML
        <div class="qr-wrapper" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
          <div id="{$containerId}" style="background: #fff; padding: 8px; border-radius: 8px; display: inline-block;">
            <img src="{$item->imgvisitorpathin}" alt="QR" style="max-width: 100%; height: auto; width: 100px; display: block; margin: 0 auto;" />
            <div style="font-size: 12px; text-align: center; color: #555; margin-top: 4px; word-break: break-all;">{$qrCodeContent}</div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="downloadQRWithContent('{$containerId}', '{$filename}')">Download</button>
        </div>
      HTML;
            })
            ->addColumn('is_kacamata', function ($item) {
                if ($item->is_kacamata === null) {
                    return '-';
                }

                return $item->is_kacamata
                    ? '<span class="badge bg-success">Ya</span>'
                    : '<span class="badge bg-secondary">Tidak</span>';
            })
            ->addColumn('kondisi_kacamata', function ($item) {
                if (!$item->is_kacamata) {
                    return '-';
                }

                return $item->kondisi_kacamata;
            })
            ->editColumn('NAMAVISITOR', fn($item) => $item->NAMAVISITOR ?: '-')
            ->editColumn('NAMACOMP', fn($item) => $item->NAMACOMP ?: '-')
            ->editColumn('PURPOSE', fn($item) => $item->PURPOSE ?: '-')
            ->editColumn('GATEIDIN', fn($item) => $item->GATEIDIN ?: '-')
            ->editColumn('GATELINEIDIN', fn($item) => $item->GATELINEIDIN ?: '-')
            ->editColumn('TIMEIN', fn($item) => $item->TIMEIN ? date('d-m-Y H:i', strtotime($item->TIMEIN)) : '-')
            ->editColumn('DATEIN', fn($item) => $item->DATEIN ? date('d-m-Y', strtotime($item->DATEIN)) : '-')
            ->editColumn('NOHPDRIVER', fn($item) => $item->NOHPDRIVER ?: '-')
            ->addColumn('action', function ($item) {
                return '<div class="dropdown d-inline-block">
            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ri-more-fill align-middle"></i>
            </button>

        </div>';
                // return '<div class="dropdown d-inline-block">
                //     <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                //       <i class="ri-more-fill align-middle"></i>
                //     </button>
                //     <ul class="dropdown-menu dropdown-menu-end">
                //       <li><a href="#" class="dropdown-item" onclick="viewVisitorDetail(\'' . $item->trnvisitorid . '\')"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>View Detail</a></li>
                //       <li><a href="#" class="dropdown-item text-danger" onclick="blockVisitor(\'' . $item->trnvisitorid . '\')"><i class="ri-close-circle-fill align-bottom me-2 text-muted"></i>Block Visitor</a></li>
                //     </ul>
                // </div>';
            })
            ->rawColumns(['photo_visitor', 'img_visitor', 'qr_image', 'namacomp', 'waktu_masuk', 'waktu_keluar', 'namavisitor', 'action', "is_kacamata", "kondisi_kacamata"])
            ->make(true);
    }


    // private function DrawTable($query)
    // {
    //   return DataTables::of($query)
    //     ->addIndexColumn()
    //     ->addColumn('qr_image', function ($item) {
    //       if (empty($item->IMGVISITORPATHIN)) {
    //         return '-';
    //       }

    //       $qrCodeContent = $item->TRNVISITORID; // Bisa dipakai ID visitor sebagai QR Code Content
    //       $containerId = 'qr-container-' . $item->TRNVISITORID;
    //       $filename = addslashes($qrCodeContent . '.png');

    //       return <<<HTML
    //         <div class="qr-wrapper" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
    //             <div id="{$containerId}" style="background: #fff; padding: 8px; border-radius: 8px; display: inline-block;">
    //                 <img src="{$item->IMGVISITORPATHIN}" alt="QR" style="max-width: 100%; height: auto; width: 100px; display: block; margin: 0 auto;" />
    //                 <div style="font-size: 12px; text-align: center; color: #555; margin-top: 4px; word-break: break-all;">{$qrCodeContent}</div>
    //             </div>
    //             <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="downloadQRWithContent('{$containerId}', '{$filename}')">Download</button>
    //         </div>
    //       HTML;
    //     })
    //     ->editColumn('NAMAVISITOR', function ($item) {
    //       return $item->NAMAVISITOR ?: '-';
    //     })
    //     ->editColumn('NAMACOMP', function ($item) {
    //       return $item->NAMACOMP ?: '-';
    //     })
    //     ->editColumn('PURPOSE', function ($item) {
    //       return $item->PURPOSE ?: '-';
    //     })
    //     ->editColumn('GATEIDIN', function ($item) {
    //       return $item->GATEIDIN ?: '-';
    //     })
    //     ->editColumn('GATELINEIDIN', function ($item) {
    //       return $item->GATELINEIDIN ?: '-';
    //     })
    //     ->editColumn('TIMEIN', function ($item) {
    //       return $item->TIMEIN ? date('d-m-Y H:i', strtotime($item->TIMEIN)) : '-';
    //     })
    //     ->editColumn('DATEIN', function ($item) {
    //       return $item->DATEIN ? date('d-m-Y', strtotime($item->DATEIN)) : '-';
    //     })
    //     ->editColumn('NOHPDRIVER', function ($item) {
    //       return $item->NOHPDRIVER ?: '-';
    //     })
    //     ->addColumn('action', function ($item) {
    //       return '
    //         <div class="dropdown d-inline-block">
    //             <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    //                 <i class="ri-more-fill align-middle"></i>
    //             </button>
    //             <ul class="dropdown-menu dropdown-menu-end">
    //                 <li><a href="#!" class="dropdown-item" onclick="viewVisitorDetail(\'' . $item->TRNVISITORID . '\')"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>View Detail</a></li>
    //                 <li><a href="#!" class="dropdown-item text-danger" onclick="blockVisitor(\'' . $item->TRNVISITORID . '\')"><i class="ri-close-circle-fill align-bottom me-2 text-muted"></i>Block Visitor</a></li>
    //             </ul>
    //         </div>';
    //     })
    //     ->rawColumns(['qr_image', 'action'])
    //     ->make(true);
    // }
}
