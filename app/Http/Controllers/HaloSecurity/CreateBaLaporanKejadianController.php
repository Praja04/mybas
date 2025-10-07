<?php

namespace App\Http\Controllers\HaloSecurity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateBaLaporanKejadianController extends Controller
{
    public function index()
    {
        $this->permission('hs_buat_lk');
        
        return view('pages.halo-security.create-ba-laporan-kejadian');
    }
}
