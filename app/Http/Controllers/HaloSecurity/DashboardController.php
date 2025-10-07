<?php

namespace App\Http\Controllers\HaloSecurity;

use App\HaloSecurity\BaIntrogasi;
use App\HaloSecurity\BaLaporanKejadian;
use App\HaloSecurity\BaSopKaryawan;
use App\HaloSecurity\BaSopSupir;
use App\HaloSecurity\SecurityUserGA;
use App\HaloSecurity\TemplateBaiItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahKejadian = BaLaporanKejadian::count();
        $jumlahKaryawan = BaSopKaryawan::count();
        $jumlahSupir = BaSopSupir::count();
        $jumlahIntrogasi = BaIntrogasi::count();
        $jumlahTemplate = TemplateBaiItems::count();
        $jumlahSug = SecurityUserGA::count();

        return view('pages.halo-security.index', compact('jumlahKejadian', 'jumlahKaryawan', 'jumlahSupir', 'jumlahIntrogasi', 'jumlahTemplate', 'jumlahSug'));
    }
}
