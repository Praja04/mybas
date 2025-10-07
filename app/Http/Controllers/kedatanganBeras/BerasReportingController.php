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
use Exception;

class BerasReportingController extends Controller
{
    public function getPageTableReporting()
    {
        return view('hr.cateringbas.pengambilan-beras-reporting.laporan-report-bulanan');
        // laporan-report-bulanan.blade.php
    }

    // tugas buat function disini
    public function getAllReportingBeras()
    {
        $dataStock = BerasStock::all();

        $totalDataStock = [];

        foreach ($dataStock as $kedatanganStockBeras) {

            $pesanan = [
                'id' => $kedatanganStockBeras->id,
                'id_stock' => $kedatanganStockBeras->id_stock,
                'tanggal' => Carbon::parse($kedatanganStockBeras->tanggal)->format('Y-m-d'),
                'kedatangan_stock' => $kedatanganStockBeras->kedatangan_stock,
                'satuan_berat' => $kedatanganStockBeras->satuan_berat,
                'status_approval' => $kedatanganStockBeras->status_approval,
            ];

            $totalDataStock[] = $pesanan;
        }

        $response = [
            'data' => $totalDataStock,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    // public function reporting($id_stock)
    // {
    //     $stock = BerasJumlah::where('id_stock', $id_stock)->get();


    //     return view('hr.cateringbas.pengambilan-beras-reporting.report-pdf-beras', compact('stock'));
    // }

    public function reporting($id_stock)
    {
        $stock = BerasJumlah::where('id_stock', $id_stock)->get();

        $pdf =  PDF::loadview('hr.cateringbas.pengambilan-beras-reporting.report-pdf-beras', compact('stock'));
        $pdf->setPaper('A4', 'potrait');

        return $pdf->download('report.pdf');
    }

    public function laporanKedatanganBeras(Request $request)
    {
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

        return view('hr.cateringbas.pengambilan-beras-reporting.laporan-kedatangan-beras', compact('jumlahStock', 'latestBerasJumlah', 'transaksiMasukCount', 'transaksiKeluarCount'));
    }
    public function laporanPengeluaranBeras()
    {
        $pengambilanBeras = BerasPengambilan::all();
        $satuanBerat = BerasSatuanBerat::all();
        $berasPemakaian = BerasPemakaian::paginate(5);
        return view('hr.cateringbas.pengambilan-beras-reporting.laporan-pengeluaran-beras', compact('pengambilanBeras', 'satuanBerat', 'berasPemakaian'));
    }

    private function generateNewIdStock($tableName)
    {
        $currentDate = Carbon::now()->format('dmY');
        
        if ($tableName == 'BerasPengambilan') {
            $latestRecord = BerasPengambilan::orderBy('id_stock', 'desc')->first();
        } elseif ($tableName == 'BerasJumlah') {
            $latestRecord = BerasJumlah::orderBy('id_stock', 'desc')->first();
        } else {
            throw new Exception("Data table tidak ditemukan");
        }

        if (!$latestRecord) {
            return 'BERAS-' . $currentDate . '-0001';
        }

        list($prefix, $date, $sequence) = explode('-', $latestRecord->id_stock);

        if ($date === $currentDate) {
            $sequence++;
            $formattedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
            return 'BERAS-' . $currentDate . '-' . $formattedSequence;
        } else {
            return 'BERAS-' . $currentDate . '-0001';
        }
    }

    public function adjustmentKedatanganBeras(Request $request)
    {
        $id = $request->id;
        $berasJumlah = BerasJumlah::find($id);
    
        if ($request->jenis_adjustment == "kurang") {
            $adjustmentAmount = $request->jumlah_keterangan_kurang;
        } else {
            $adjustmentAmount = $request->jumlah_keterangan_tambah; 
        } 

        // dd($request->all());

        // if ($request->has('jumlah_keterangan_kurang')) {
        //     $transaksiKeluar = $request->jumlah_keterangan_kurang;
        // }
    
        $berasJumlahTerakhir = BerasJumlah::where('id_stock', '<>', '')
            ->orderBy('id_stock', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($berasJumlahTerakhir && $berasJumlahTerakhir->adjustment == 1) {
            return response()->json(['error' => 'Transaksi sudah melakukan adjustment. Silahkan tunggu kedatangan transaksi berikutnya.']);
        } else {
            $previousStock = optional($berasJumlahTerakhir)->jumlah_stock_sesudah ?? 0;
    
            $newIdStock = $this->generateNewIdStock('BerasJumlah');
            $newBerasJumlah = new BerasJumlah();
            $newBerasJumlah->id_stock = $newIdStock;
            $newBerasJumlah->tanggal = Carbon::now();
            $newBerasJumlah->tanggal_beras = Carbon::now()->toDateString();
            $newBerasJumlah->jumlah_stock = $previousStock;

            if ($request->jenis_adjustment == "kurang") {
                $newBerasJumlah->transaksi_keluar = $adjustmentAmount;
            } else {
                $newBerasJumlah->transaksi_masuk = $adjustmentAmount; 
            }
            
            $newBerasJumlah->adjustment_kedatangan = null;

            if ($request->jenis_adjustment == 'kurang') {
                $newBerasJumlah->jumlah_stock_sesudah =  $berasJumlah->jumlah_stock_sesudah - $adjustmentAmount; 
            } else {
                $newBerasJumlah->jumlah_stock_sesudah =  $berasJumlah->jumlah_stock_sesudah + $adjustmentAmount; 
            }
        
            $newBerasJumlah->keterangan = '';
            $newBerasJumlah->adjustment = 1;
            $newBerasJumlah->status = 'adjustment';
            $newBerasJumlah->active = 'Y';
            $newBerasJumlah->satuan_berat = 'sak';
            $newBerasJumlah->status_approval = null;
            $newBerasJumlah->approved_at = null;
            $newBerasJumlah->approved_by = null;
            $newBerasJumlah->created_at = Carbon::now();
            $newBerasJumlah->updated_at = Carbon::now();
    
            $newBerasJumlah->save();
    
            return response()->json(['success' => 1, 'message' => 'Adjustment kedatangan berhasil dilakukan']);
        }
    }

    private function generateIdReportingbrs()
    {
        $latestPengambilan = BerasPengambilan::orderBy('id_pengambilan', 'desc')->first();
        $currentDate = Carbon::now()->format('dmY'); 

        if (!$latestPengambilan) {
            return 'BP-' . $currentDate . '-0001';
    }
        list($awalan, $tanggal, $urutan) = explode('-', $latestPengambilan->id_pengambilan);

        $urutan = str_pad($urutan + 1, 4, '0', STR_PAD_LEFT);
        
        return 'BP-' . $tanggal . '-' . $urutan;
    }
    
    public function adjustmentPengeluaranBeras(Request $request)
    {
        $id = $request->id;
        $berasJumlah = BerasPengambilan::find($id);
    
        if ($request->jenis_adjustment == "kurang") {
            $adjustmentAmount = $request->jumlah_keterangan_kurang;
        } else {
            $adjustmentAmount = $request->jumlah_keterangan_tambah; 
        } 

        // dd($request->all());

        // if ($request->has('jumlah_keterangan_kurang')) {
        //     $transaksiKeluar = $request->jumlah_keterangan_kurang;
        // }
    
        $berasJumlahTerakhir = BerasPengambilan::where('id_stock', '<>', '')
            ->orderBy('id_stock', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($berasJumlahTerakhir && $berasJumlahTerakhir->adjustment == 1) {
            return response()->json(['error' => 'Transaksi sudah melakukan adjustment. Silahkan tunggu kedatangan transaksi berikutnya.']);
        } else {
            $previousStock = optional($berasJumlahTerakhir)->jumlah_pengambilan_sesudah ?? 0;
    
            $newIdStock = $this->generateNewIdStock('BerasPengambilan');
            $id_pengambilan = $this->generateIdReportingbrs();
            $newBerasJumlah = new BerasPengambilan();
            $newBerasJumlah->id_stock = $newIdStock;
            $newBerasJumlah->id_pengambilan = $id_pengambilan;
            $newBerasJumlah->tanggal = Carbon::now();
            $newBerasJumlah->tanggal_beras = Carbon::now()->toDateString();
            $newBerasJumlah->jumlah_pengambilan_sebelum = $previousStock;

            if ($request->jenis_adjustment == "kurang") {
                $newBerasJumlah->transaksi_keluar = $adjustmentAmount;
            } else {
                $newBerasJumlah->transaksi_masuk = $adjustmentAmount; 
            }
            
            $newBerasJumlah->adjustment_kedatangan = null;

            if ($request->jenis_adjustment == 'kurang') {
                $newBerasJumlah->jumlah_pengambilan_sesudah =  $berasJumlah->jumlah_pengambilan_sesudah - $adjustmentAmount; 
            } else {
                $newBerasJumlah->jumlah_pengambilan_sesudah =  $berasJumlah->jumlah_pengambilan_sesudah + $adjustmentAmount; 
            }
        
            $newBerasJumlah->keterangan = '';
            $newBerasJumlah->adjustment = 1;
            $newBerasJumlah->status = 'adjustment';
            $newBerasJumlah->satuan_berat = 'liter';
            $newBerasJumlah->approved_at = null;
            $newBerasJumlah->approved_by = null;
            $newBerasJumlah->created_at = Carbon::now();
            $newBerasJumlah->updated_at = Carbon::now();
    
            $newBerasJumlah->save();
    
            return response()->json(['success' => 1, 'message' => 'Adjustment kedatangan berhasil dilakukan']);
        }
    }

    public function getDateKedatanganBeras(Request $request){
        $tanggalAwal = Carbon::parse($request->input('tanggal_awal'))->format('Y-m-d');
        $tanggalAkhir = Carbon::parse($request->input('tanggal_akhir'))->format('Y-m-d');
    
        $data = BerasJumlah::whereDate('tanggal', '>=', $tanggalAwal)
                        ->whereDate('tanggal', '<=', $tanggalAkhir)
                        ->orderBy('id_stock', 'desc')
                        ->orderBy('tanggal', 'desc')
                        ->get();
    
        return response()->json($data);
    }
    
}
