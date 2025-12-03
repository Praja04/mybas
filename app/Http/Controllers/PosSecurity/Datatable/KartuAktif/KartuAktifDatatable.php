<?php

namespace App\Http\Controllers\PosSecurity\Datatable\KartuAktif;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KartuAktifDatatable extends Controller
{
    public function index(Request $request)
    {
        // Tidak perlu memanggil rawData seperti di KartuAktif
        // Kita akan membangun query builder untuk DataTables
        // Query akan dibangun langsung di DrawTable atau bisa juga di sini
        // Untuk fleksibilitas, kita bisa saja membangun query dasar di sini
        // Tapi karena DataTables membutuhkan Query Builder untuk serverSide,
        // kita akan bangun logikanya di DrawTable.

        // Kita bisa saja mengirimkan filter ke DrawTable
        return $this->DrawTable($request);
    }

    private function DrawTable(Request $request)
    {
        // Ambil parameter filter
        $filter = $request->query('filter', []);

        // Tentukan POS dan tabel
        $pos = $filter['pos'] ?? 'POS 2'; // Default ke POS 2
        $table = ($pos === 'POS 2') ? 'ga_visitor_vendor' : 'ga_visitor_transaction'; // Sesuaikan nama tabel

        // Bangun query dasar menggunakan Query Builder untuk agregasi
        $query = DB::table($table)
            ->selectRaw("no_kartu as nomor_kartu, COUNT(*) as jumlah_penggunaan, type")
            ->whereNotNull('no_kartu')
            ->where('no_kartu', '!=', '')
            ->groupBy('no_kartu', 'type')
            ->orderBy('jumlah_penggunaan', 'DESC');

        // Filter berdasarkan 'type' dari request
        if (!empty($filter['type'])) {
            $query->where('type', $filter['type']);
        }

        // Filter berdasarkan 'no_kartu' dari request (filter[no_kartu])
        if (!empty($filter['no_kartu'])) {
            $query->havingRaw("no_kartu LIKE ?", ['%' . $filter['no_kartu'] . '%']);
        }

        // Return DataTables instance
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($item) {
                // Pastikan $item->nomor_kartu tidak null atau kosong
                if (empty($item->nomor_kartu)) {
                    // Jika nomor_kartu kosong, kembalikan pesan atau tindakan alternatif
                    return '<span class="text-danger">Nomor Kartu Tidak Valid</span>';
                }

                // todo
                // Buat URL menggunakan helper route
                // $url = route('portal.ga.sistem-tracking.kartu.pengguna_kartu', ['nomor_kartu' => $item->nomor_kartu]);

                // Buat tombol HTML
                // return '<a href="' . e($url) . '" class="btn btn-sm btn-primary">Detail</a>';
                return '<a href="#" class="btn btn-sm btn-primary">Detail</a>';
            })
            // Tambahkan 'action' ke rawColumns karena sekarang berisi HTML
            ->rawColumns(['action'])
            ->make(true);
    }
}
