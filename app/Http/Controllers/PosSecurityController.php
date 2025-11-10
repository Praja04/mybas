<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosSecurityController extends Controller
{
    public function index()
    {
        return view('pos-security.dashboard.index');
    }

    public function form()
    {
        return view('pos-security.formulir.index');
    }

    public function formTamu()
    {
        return view('pos-security.formulir.tamu.index');
    }

    public function formSupplier()
    {
        return view('pos-security.formulir.supplier.index');
    }

    public function display()
    {
        return view('pos-security.display.index');
    }
}
