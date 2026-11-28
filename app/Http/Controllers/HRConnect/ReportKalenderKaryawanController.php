<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ReportKalenderKaryawanController extends Controller
{
    // $id gua ganti jadi $date biar logikanya bener, karena dari frontend dikirimnya string tanggal (YYYY-MM-DD)
    public function getReportIn($date)
    {
        // OPTIMASI: Panggil yg bener-bener udah diproses Admin aja (in_kode_group = Y)
        $report = HrKaryawan::select('nama', 'nik', 'tanggal_masuk', 'kode_bagian')
            ->where('in_kode_group', 'Y')
            ->whereDate('tanggal_masuk', $date)
            ->get();

        return response()->json($report);
    }

    public function getReportOut($date)
    {
        // OPTIMASI: Panggil yg bener-bener udah di-checkout Admin aja (is_excuse_out = Y)
        $report = HrKaryawan::select('nama', 'nik', 'tanggal_keluar', 'kode_bagian')
            ->where('is_excuse_out', 'Y')
            ->whereDate('tanggal_keluar', $date)
            ->get();

        return response()->json($report);
    }

    public function index()
    {
        $data['title'] = 'Report Kalender Karyawan';

        // BIKIN DINAMIS: Ambil bulan dan tahun SAAT INI, jangan di-hardcode 10!
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // Ambil 10 karyawan terbaru (Bisa buat ditaruh di sidebar view)
        $data['karyawans'] = HrKaryawan::select('nama', 'tanggal_masuk', 'nik', 'kode_bagian')
            ->where('in_kode_group', 'Y')
            ->orderBy('tanggal_masuk', 'desc')
            ->take(10)
            ->get();

        // Data karyawan masuk bulan INI (Bukan Oktober doang)
        $data['karyawan_masuk'] = HrKaryawan::select('nama', 'tanggal_masuk', 'nik')
            ->where('in_kode_group', 'Y')
            ->whereNotNull('tanggal_masuk')
            ->where('tanggal_masuk', '!=', '0000-00-00')
            ->whereMonth('tanggal_masuk', $currentMonth)
            ->whereYear('tanggal_masuk', $currentYear)
            ->get();

        // Data karyawan keluar bulan INI
        $data['karyawan_keluar'] = HrKaryawan::select('nama', 'tanggal_keluar', 'tanggal_masuk', 'nik')
            ->where('is_excuse_out', 'Y')
            ->whereNotNull('tanggal_keluar')
            ->where('tanggal_keluar', '!=', '0000-00-00')
            ->whereMonth('tanggal_keluar', $currentMonth)
            ->whereYear('tanggal_keluar', $currentYear)
            ->get();

        return view('hr-connect.report.kalendar_karyawan', $data);
    }
}
