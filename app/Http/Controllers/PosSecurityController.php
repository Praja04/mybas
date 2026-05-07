<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class PosSecurityController extends Controller
{
    // form menu view
    public function form()
    {
        return view('pos-security.formulir.index');
    }

    public function formSupplier()
    {
        return view('pos-security.formulir.supplier.index');
    }

    public function formTamu()
    {
        $departments = Department::where('status', '1')->get();
        return view('pos-security.formulir.tamu.index', compact('departments'));
    }

    public function formCekKendaraan()
    {
        return view('pos-security.formulir.cek-kendaraan.index');
    }

    public function dashboard()
    {
        return view('pos-security.dashboard.index');
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

    public function blacklist()
    {
        return view('pos-security.blacklist.index');
    }

    public function kartuAktif()
    {
        return view('pos-security.kartu.index');
    }

    public function resetKartu(Request $request)
    {
        $request->validate([
            'trnvisitorid' => 'required|string',
            'type' => 'required|string' // 'supplier' or 'vendor'
        ]);

        try {
            $now = now();
            $data = [
                'kartu_dikembalikan' => true,
                'dateout' => $now->toDateString(),
                'timeout' => $now->toTimeString(),
                'changedon' => $now,
                'changedby' => auth()->user()->username ?? 'system',
            ];

            if ($request->type === 'supplier') {
                \App\Models\PosSecurity\GaVisitorTransaction::where('trnvisitorid', $request->trnvisitorid)->update($data);
            } else {
                \App\Models\PosSecurity\GaVisitorVendorTransaction::where('trnvisitorid', $request->trnvisitorid)->update($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kartu berhasil direset dan transaksi ditutup.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset kartu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function kartuAktifDetail()
    {
        return view('pos-security.kartu.detail');
    }

    public function historySupplier()
    {
        return view("pos-security.history-supplier.index");
    }

    public function historyVendor()
    {
        return view("pos-security.history-tamu.index");
    }

    public function historyCekKendaraan()
    {
        return view("pos-security.history-cek-kendaraan.index");
    }

    public function dataSecurity()
    {
        return view("pos-security.master.security.index");
    }
}
