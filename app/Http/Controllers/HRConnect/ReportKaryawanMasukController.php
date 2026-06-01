<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ReportKaryawanMasukController extends Controller
{
    public function index()
    {
        $data['title'] = 'Report Karyawan Masuk';

        $data['kodeDivisi'] = HrKaryawan::whereNotNull('kode_divisi')->where('kode_divisi', '!=', '')->distinct()->pluck('kode_divisi')->toArray();
        $data['kodeBagian'] = HrKaryawan::whereNotNull('kode_bagian')->where('kode_bagian', '!=', '')->distinct()->pluck('kode_bagian')->toArray();
        $data['kodeGroup']  = HrKaryawan::whereNotNull('kode_group')->where('kode_group', '!=', '')->distinct()->pluck('kode_group')->toArray();

        // Bersih! Karena kolomnya ENUM, kita langsung tarik aja dengan aman
        $data['lokers'] = DB::table('loker_transaksi')
            ->select('kode_rak as kode_blok', 'no_loker')
            ->where('tipe_transaksi', 'MASUK')
            ->distinct()
            ->orderBy('kode_rak', 'asc')
            ->orderBy('no_loker', 'asc')
            ->get();

        return view('hr-connect.report.karyawan_masuk', $data);
    }

    public function getData()
    {
        $report = HrKaryawan::where([
            'in_complete'   => 'Y',
            'in_kode_group' => 'Y',
        ])
            ->whereNotNull('tanggal_masuk')
            ->where('tanggal_masuk', '!=', '0000-00-00')
            ->leftJoin('loker_transaksi', function ($join) {
                $join->on(DB::raw('loker_transaksi.nik COLLATE utf8mb4_unicode_ci'), '=', 'hr_karyawan.nik')
                    ->where('loker_transaksi.tipe_transaksi', '=', 'MASUK')
                // Pengaman Ganda: Biar NIK yang Re-Hire gak narik loker 5 tahun lalu
                    ->whereRaw('DATE(loker_transaksi.created_at) >= hr_karyawan.tanggal_masuk');
            })
            ->select(
                'hr_karyawan.id',
                'hr_karyawan.nama',
                'hr_karyawan.nik',
                'hr_karyawan.kode_divisi',
                'hr_karyawan.kode_bagian',
                'hr_karyawan.kode_group',
                'hr_karyawan.tanggal_masuk',
                'loker_transaksi.kode_rak as kode_blok',
                'loker_transaksi.no_loker'
            )
            ->groupBy(
                'hr_karyawan.id',
                'hr_karyawan.nama',
                'hr_karyawan.nik',
                'hr_karyawan.kode_divisi',
                'hr_karyawan.kode_bagian',
                'hr_karyawan.kode_group',
                'hr_karyawan.tanggal_masuk',
                'loker_transaksi.kode_rak',
                'loker_transaksi.no_loker'
            )
            ->orderBy('hr_karyawan.tanggal_masuk', 'desc');

        return Datatables::of($report)->make(true);
    }
}
