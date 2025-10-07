<?php

namespace App\Http\Controllers\HaloSecurity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateBaIntrogasiController extends Controller
{
    public function index()
    {
        $this->permission('hs_createbai');
        
        return view('pages.halo-security.create-ba-introgasi');
    }
}
