<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ReportKaryawanKeluarController extends Controller
{
    public function index()
    {
        $data['title'] = 'Report Karyawan Keluar';

        $data['kodeDivisi'] = HrKaryawan::whereNotNull('kode_divisi')->where('kode_divisi', '!=', '')->distinct()->pluck('kode_divisi')->toArray();
        $data['kodeBagian'] = HrKaryawan::whereNotNull('kode_bagian')->where('kode_bagian', '!=', '')->distinct()->pluck('kode_bagian')->toArray();
        $data['kodeGroup']  = HrKaryawan::whereNotNull('kode_group')->where('kode_group', '!=', '')->distinct()->pluck('kode_group')->toArray();

        // Ambil sejarah loker khusus tipe KELUAR
        $data['lokers'] = DB::table('loker_transaksi')
            ->select('kode_rak as kode_blok', 'no_loker')
            ->where('tipe_transaksi', 'KELUAR')
            ->distinct()
            ->orderBy('kode_rak', 'asc')
            ->orderBy('no_loker', 'asc')
            ->get();

        return view('hr-connect.report.karyawan_keluar', $data);
    }

    public function getData()
    {
        $report = HrKaryawan::where([
            'in_complete'   => 'Y',
            'in_kode_group' => 'Y',
            'is_excuse_out' => 'Y',
            'out_complete'  => 'Y',
            'checked_ir'    => 'Y', // LOGIKA BISNIS: Harus sudah di-finalisasi oleh HRD IR!
        ])
            ->whereNotNull('tanggal_keluar')
            ->where('tanggal_keluar', '!=', '0000-00-00')
            ->leftJoin('loker_transaksi', function ($join) {
                $join->on(DB::raw('loker_transaksi.nik COLLATE utf8mb4_unicode_ci'), '=', 'hr_karyawan.nik')
                    ->where('loker_transaksi.tipe_transaksi', '=', 'KELUAR')
                // PENGAMAN RE-HIRE: Hanya ambil transaksi keluar yang terjadi SETELAH tanggal dia masuk terakhir kali
                    ->whereRaw('DATE(loker_transaksi.created_at) >= hr_karyawan.tanggal_masuk');
            })
            ->select(
                'hr_karyawan.id',
                'hr_karyawan.nama',
                'hr_karyawan.nik',
                'hr_karyawan.kode_divisi',
                'hr_karyawan.kode_bagian',
                'hr_karyawan.kode_group',
                'hr_karyawan.tanggal_keluar',
                'loker_transaksi.kode_rak as kode_blok',
                'loker_transaksi.no_loker'
            )
        // GROUP BY: Mencegah row beranak-pinak jika ada histori ganda
            ->groupBy(
                'hr_karyawan.id',
                'hr_karyawan.nama',
                'hr_karyawan.nik',
                'hr_karyawan.kode_divisi',
                'hr_karyawan.kode_bagian',
                'hr_karyawan.kode_group',
                'hr_karyawan.tanggal_keluar',
                'loker_transaksi.kode_rak',
                'loker_transaksi.no_loker'
            )
            ->orderBy('hr_karyawan.tanggal_keluar', 'desc');

        return Datatables::of($report)->make(true);
    }
}
