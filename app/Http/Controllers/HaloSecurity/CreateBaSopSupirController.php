<?php

namespace App\Http\Controllers\HaloSecurity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateBaSopSupirController extends Controller
{
    public function index()
    {
        $this->permission('hs_buat_sopsupir');
        
        return view('pages.halo-security.create-ba-so-supir');
    }
}
