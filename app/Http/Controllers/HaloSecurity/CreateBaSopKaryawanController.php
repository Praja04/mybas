<?php

namespace App\Http\Controllers\HaloSecurity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateBaSopKaryawanController extends Controller
{
    public function index()
    {
        $this->permission('hs_buat_sopkaryawan');
        
        return view('pages.halo-security.create-ba-so-karyawan');
    }
}
