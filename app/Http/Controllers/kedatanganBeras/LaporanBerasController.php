<?php

namespace App\Http\Controllers\kedatanganBeras;

use App\Imports\Ecafesedaap\OvertimeImport;
use Illuminate\Support\Facades\DB;
use Session;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use App\BerasPemakaian;
use App\BerasPengambilan;
use App\BerasSatuanBerat;
use App\BerasStock;
use App\BerasJumlah;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanBerasController extends Controller
{
    public function laporanKedatangan(Request $request){
        if ($request->has('search')) {
            $jumlahStock = BerasJumlah::where('tanggal', 'LIKE', '%' . $request->search)->paginate(6);
            $transaksiMasukCount = BerasJumlah::where('tanggal', 'LIKE', '%' . $request->search)->sum('transaksi_masuk');
            $transaksiKeluarCount = BerasJumlah::where('tanggal', 'LIKE', '%' . $request->search)->sum('transaksi_keluar');
        } else {
            $jumlahStock = BerasJumlah::orderBy('tanggal', 'asc')->paginate(6);
            $transaksiMasukCount = BerasJumlah::sum('transaksi_masuk');
            $transaksiKeluarCount = BerasJumlah::sum('transaksi_keluar');
        }
        $piechart = BerasJumlah::latest()->first();
        $latestBerasJumlah = $piechart ? $piechart->jumlah_stock : 0;

        return view('hr.cateringbas.pengambilan-beras.jumlah-beras', compact('jumlahStock', 'latestBerasJumlah', 'transaksiMasukCount', 'transaksiKeluarCount'));
    }

    public function laporanPengeluaran(){
        return view('hr.cateringbas.pengambilan-beras-reporting.laporan-pengeluaran-beras');
    }
}
