<?php

namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class ReportKaryawanKeluarController extends Controller
{
    public function getData()
    {
        $report = HrKaryawan::where([
                            'in_complete' => 'Y',
                            'in_kode_group' => 'Y',
                            'is_excuse_out' => 'Y',
                            'out_complete' => 'Y',
                        ])
                        ->leftJoin('loker_master_user', 'loker_master_user.nik', '=', 'hr_karyawan.nik')
                        ->select('hr_karyawan.nama','hr_karyawan.nik','hr_karyawan.kode_divisi','hr_karyawan.kode_bagian','hr_karyawan.kode_group','tanggal_keluar','loker_master_user.kode_blok','loker_master_user.no_loker')
                        ->get();

        return Datatables::of($report)->make(true);
    }

    public function index()
    {
        $data['title'] = 'Report Karyawan Keluar';

        $kodeDivisi = HrKaryawan::distinct()->pluck('kode_divisi')->toArray();
        $kodeDivisi = array_unique($kodeDivisi);
        $kodeDivisi = array_filter($kodeDivisi);
        $kodeBagian = HrKaryawan::distinct()->pluck('kode_bagian')->toArray();
        $kodeBagian = array_unique($kodeBagian);
        $kodeBagian = array_filter($kodeBagian);
        $kodeGroup = HrKaryawan::distinct()->pluck('kode_group')->toArray();
        $kodeGroup = array_unique($kodeGroup);
        $kodeGroup = array_filter($kodeGroup);

        $data['kodeDivisi'] = $kodeDivisi;
        $data['kodeBagian'] = $kodeBagian;
        $data['kodeGroup'] = $kodeGroup;

        $data['lokers'] = HrKaryawan::where([
            'in_complete' => 'Y',
            'in_kode_group' => 'Y',
            'is_excuse_out' => 'Y',
            'out_complete' => 'Y',
        ])
        ->leftJoin('loker_master_user','loker_master_user.nik', '=','hr_karyawan.nik')
        ->select('loker_master_user.kode_blok', 'loker_master_user.no_loker')
        ->distinct()
        ->get();

        $data['lokers'] = $data['lokers']->sortBy(function ($loker, $key) {
            return $loker->kode_blok . ' ' . $loker->no_loker;
        });

        return view('hr-connect.report.karyawan_keluar', $data);
    }
}
