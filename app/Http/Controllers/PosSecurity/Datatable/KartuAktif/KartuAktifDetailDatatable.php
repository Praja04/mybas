<?php

namespace App\Http\Controllers\PosSecurity\Datatable\KartuAktif;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaVisitorVendorTransaction;

class KartuAktifDetailDatatable extends Controller
{
    /**
     * Handle the incoming request for the detail DataTable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 1. Pastikan filter no_kartu ada
        $noKartu = $request->get('nomor_kartu');

        if (empty($noKartu)) {
            // Jika no_kartu tidak ada, kembalikan DataTable kosong atau error
            // Cara paling sederhana untuk DataTables adalah mengembalikan koleksi kosong
            return DataTables::of(collect([]))->make(true);
            // Atau kirim error JSON jika frontend bisa menanganinya
            // return response()->json(['error' => 'Nomor kartu tidak ditemukan.'], 400);
        }

        // 2. Dapatkan data berdasarkan no_kartu
        $query = $this->rawData($request, $noKartu);

        // 3. Gambar tabel
        return $this->DrawTable($query);
    }

    /**
     * Get the raw data query for detail, filtered by no_kartu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $noKartu
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    private function rawData($request, $noKartu)
    {
        $filter = $request->query('filter', []);

        // Mulai query dengan filter no_kartu
        $query = GaVisitorVendorTransaction::query()
            ->where('no_kartu', $noKartu) // Filter utama
            ->orderBy('createdon', 'desc');

        // Filter nama visitor (opsional)
        if (!empty($filter['nama_visitor'])) {
            $query->where('namavisitor', 'like', '%' . $filter['nama_visitor'] . '%');
        }

        // Filter type (opsional)
        if (!empty($filter['type'])) {
            $query->where('type',  $filter['type']);
        }

        // Filter kartu_dikembalikan (opsional)
        if (isset($filter['kartu_dikembalikan']) && $filter['kartu_dikembalikan'] !== '') {
            $query->where('kartu_dikembalikan', (bool) $filter['kartu_dikembalikan']);
        }

        // Filter tanggal (opsional)
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            try {
                $start = Carbon::createFromFormat('d-m-Y', $filter['start_date'])->startOfDay();
                $end = Carbon::createFromFormat('d-m-Y', $filter['end_date'])->endOfDay();
                $query->whereBetween('createdon', [$start, $end]);
            } catch (\Exception $e) {
                // Log error jika perlu
                Log::warning('Invalid date format in KartuAktifDetailDatatable: ' . $e->getMessage());
            }
        }

        // --- Pertimbangan: Batasi waktu? ---
        // Untuk detail kartu, mungkin batasan 7 hari terakhir tidak diperlukan.
        // Jika tetap diinginkan, aktifkan baris berikut:
        // $sevenDaysAgo = Carbon::now()->subDays(7);
        // $query->where('createdon', '>=', $sevenDaysAgo);

        // Batasi jumlah hasil jika perlu (opsional, untuk performa)
        // $query->limit(500); // Misalnya

        return $query; // Kembalikan query builder, bukan hasil get()
        // DataTables membutuhkan query builder untuk serverSide processing
    }

    /**
     * Draw the DataTable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Http\JsonResponse
     */
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
            // Kolom-kolom berikut tampaknya duplikat atau tidak digunakan berdasarkan struktur model.
            // Jika memang tidak ada, sebaiknya dihapus untuk menghindari error.
            // ->editColumn('NAMAVISITOR', fn($item) => $item->NAMAVISITOR ?: '-') 
            // ->editColumn('NAMACOMP', fn($item) => $item->NAMACOMP ?: '-')
            // ->editColumn('PURPOSE', fn($item) => $item->PURPOSE ?: '-')
            // ->editColumn('GATEIDIN', fn($item) => $item->GATEIDIN ?: '-')
            // ->editColumn('GATELINEIDIN', fn($item) => $item->GATELINEIDIN ?: '-')
            // ->editColumn('TIMEIN', fn($item) => $item->TIMEIN ? date('d-m-Y H:i', strtotime($item->TIMEIN)) : '-')
            // ->editColumn('DATEIN', fn($item) => $item->DATEIN ? date('d-m-Y', strtotime($item->DATEIN)) : '-')
            // ->editColumn('NOHPDRIVER', fn($item) => $item->NOHPDRIVER ?: '-')

            // Tambahkan kolom yang relevan untuk detail kunjungan
            ->editColumn('purpose', fn($item) => $item->purpose ?: '-')
            ->editColumn('host', fn($item) => $item->host ?: '-')
            ->editColumn('nopol', fn($item) => $item->nopol ?: '-')

            ->addColumn('action', function ($item) {
                // Untuk detail, action mungkin lebih sedikit atau berbeda
                // Misalnya, hanya view detail atau tidak ada action sama sekali
                // Untuk sekarang, kita kosongkan atau beri placeholder
                return '<div class="text-center">-</div>';
                // Atau jika ingin tetap ada dropdown:
                // return '<div class="dropdown d-inline-block">
                //     <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                //       <i class="ri-more-fill align-middle"></i>
                //     </button>
                //     <ul class="dropdown-menu dropdown-menu-end">
                //       <li><a href="#" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>Lihat Kunjungan</a></li>
                //     </ul>
                // </div>';
            })
            ->rawColumns(['photo_visitor', 'img_visitor', 'qr_image', 'namacomp', 'waktu_masuk', 'waktu_keluar', 'namavisitor', 'action'])
            ->make(true);
    }
}
