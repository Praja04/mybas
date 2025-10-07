<?php

namespace App\Http\Controllers;

use App\DataTamu;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index()
    {
        return view('display.index');
    }

    public function kbbm()
    {
        return view('display.kbbm');
    }

    public function data_tamu()
    {
        $data_tamu = DataTamu::orderBy('tanggal', 'desc')->get();
        return view('display.data-tamu', ['data_tamu' => $data_tamu]);
    }

    public function secureAccess()
    {
        return view('display.secure-access');
    }

    public function pengambilanIdCard()
    {
        return view('display.pengambilan-id-card');
    }

    public function logging_machine()
    {
        return view('display.logging_machine');
    }
}
