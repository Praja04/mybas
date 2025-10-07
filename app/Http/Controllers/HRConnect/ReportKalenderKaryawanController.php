<?php

namespace App\Http\Controllers\HRConnect;

use Carbon\Carbon;
use App\HrKaryawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportKalenderKaryawanController extends Controller
{
    public function getReportIn($id)
    {
        $report = HrKaryawan::where('tanggal_masuk', $id)->select('nama','nik','tanggal_masuk')->get();
        
        return $report;
    }

    public function getReportOut($id)
    {
        $report = HrKaryawan::where('tanggal_keluar', $id)->select('nama','nik','tanggal_keluar')->get();
        
        return $report;
    }

    public function index()
    {
        $today = Carbon::today()->toDateString();
        $data['karyawans'] = HrKaryawan::orderBy('tanggal_masuk','desc')->select('nama','tanggal_masuk')->take(10)->get();
        $data['karyawan_masuk'] = HrKaryawan::select('nama','tanggal_masuk','nik')
                                            ->whereNotNull('tanggal_masuk')
                                            ->whereMonth('tanggal_masuk', 10)
                                            ->get();

        $data['karyawan_keluar'] = HrKaryawan::select('nama','tanggal_keluar','tanggal_masuk','nik')
                                                ->whereNotNull('tanggal_masuk')
                                                ->whereNotNull('tanggal_keluar')
                                                ->whereMonth('tanggal_keluar', 10)
                                                ->get();

        return view('hr-connect.report.kalendar_karyawan', $data);
    }
}
