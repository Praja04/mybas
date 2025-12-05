<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosSecurityController extends Controller
{
    // controller sementara
    public function index()
    {
        return view('pos-security.dashboard.index');
    }

    public function form()
    {
        return view('pos-security.formulir.index');
    }

    public function formSupplier()
    {
        return view('pos-security.formulir.supplier.index');
    }

    public function display()
    {
        return view('pos-security.display.index');
    }

    public function absensiVisitor()
    {
        return view('pos-security.absensi.index');
    }

    public function absensiGate()
    {
        return view('pos-security.absensi.gate');
    }

    public function formCekKendaraan()
    {
        return view('pos-security.cek-kendaraan.index');
    }

    public function blacklist()
    {
        return view('pos-security.blacklist.index');
    }

    public function kartuAktif()
    {
        return view('pos-security.kartu.index');
    }

    public function kartuAktifDetail()
    {
        return view('pos-security.kartu.detail');
    }
}
