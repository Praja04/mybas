<?php

namespace App\Http\Controllers\kedatanganBeras;

use App\Imports\Ecafesedaap\OvertimeImport;
use Illuminate\Support\Facades\DB;
use Session;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Carbon\Carbon;
use App\BerasPemakaian;
use App\BerasPengambilan;
use App\BerasStock;
use App\BerasJumlah;
use App\BerasSatuanBerat;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockReminder;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Jobs\SendStockReminderEmailJumlahBeras ;
use App\Exports\BufferStockBerasExport;
use Exception;
use App\Jobs\SendTelegramMessageJob;

class pengambilanBerasController extends Controller
{
    public function halamanPengambilan()
    {
        $pengambilanBeras = BerasJumlah::all();
        $satuanBerat = BerasSatuanBerat::all();
        
        $currentMonth = date('m');
        
        $pengambilanBulanIni = BerasPengambilan::whereMonth('tanggal', '=', $currentMonth)->get();
        $totalJumlahPengambilan = $pengambilanBulanIni->sum('jumlah_pengambilan');

        $records = BerasPengambilan::first();
        return view('hr.cateringbas.pengambilan-beras.pengambilan-beras', compact('pengambilanBeras', 'satuanBerat', 'records', 'totalJumlahPengambilan'));
    }

    private function generateIdPengambilan()
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


    public function ambilstockBeras(Request $request)
    {
        try {
            $idStock = $request->id_stock; 
            $id_pengambilan = $this->generateIdPengambilan();
            $currentDate = Carbon::now()->format('Y-m-d H:i:s');
            $tanggalBerasTanpaWaktu = Carbon::now()->format('Y-m-d');
    
            $lastBerasPengambilan = BerasPengambilan::latest()->first();
            $jumlah_pengambilan_sebelum = $lastBerasPengambilan ? $lastBerasPengambilan->jumlah_pengambilan_sesudah : 0;
            $jumlah_pengambilan = $request->jumlah_pengambilan;
            $jumlah_pengambilan_sesudah = $jumlah_pengambilan_sebelum + $jumlah_pengambilan;
    
            $lastBerasPengambilan = BerasPengambilan::latest()->first();
            $jumlah_pengambilan_sebelum_liter = $lastBerasPengambilan ? $lastBerasPengambilan->jumlah_pengambilan_sesudah : 0;
            $jumlah_pengambilan_liter = $request->jumlah_pengambilan * 60;
            $jumlah_pengambilan_sesudah_liter = $jumlah_pengambilan_sebelum_liter + $jumlah_pengambilan_liter;
    
            $berasJumlahTerakhir = BerasJumlah::where('id_stock', '<>', '')
                ->orderBy('id_stock', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
    
            if ($berasJumlahTerakhir && $berasJumlahTerakhir->jumlah_stock_sesudah !== null) {
                $newJumlahStock = $berasJumlahTerakhir->jumlah_stock_sesudah - $jumlah_pengambilan;
    
                if ($newJumlahStock >= 0) {
                    $stockberas = new BerasPengambilan();
                    $idStockBerasPengambilan = $this->generateNewIdStock('BerasPengambilan'); 
                    $stockberas->id_stock = $idStockBerasPengambilan;
                    $stockberas->id_pengambilan = $id_pengambilan;
                    $stockberas->tanggal = $currentDate;
                    $stockberas->tanggal_beras = $tanggalBerasTanpaWaktu;
                    $stockberas->jumlah_pengambilan_sebelum = $jumlah_pengambilan_sebelum_liter;
                    $stockberas->shift = $request->shift;
                    $stockberas->keterangan = null;
                    $stockberas->jumlah_pengambilan = $jumlah_pengambilan;
                    $stockberas->transaksi_masuk = $jumlah_pengambilan;
                    $stockberas->jumlah_pengambilan_sesudah = $jumlah_pengambilan_sesudah_liter;
                    $stockberas->status = 'in';
                    $stockberas->satuan_berat = 'liter';
                    $stockberas->save();
    
                    $newIdStockBerasJumlah = $this->generateNewIdStock('BerasJumlah'); 
                    $berasJumlahBaru = new BerasJumlah();
                    $berasJumlahBaru->id_stock = $newIdStockBerasJumlah;
                    $berasJumlahBaru->tanggal = $currentDate;
                    $berasJumlahBaru->tanggal_beras = $tanggalBerasTanpaWaktu;
                    $berasJumlahBaru->transaksi_keluar = $jumlah_pengambilan;
                    $berasJumlahBaru->jumlah_stock = $berasJumlahTerakhir->jumlah_stock_sesudah;
                    $berasJumlahBaru->jumlah_stock_sesudah = $newJumlahStock;
                    $berasJumlahBaru->status = 'out';
                    $berasJumlahBaru->active = 'y';
                    $berasJumlahBaru->satuan_berat = 'sak';
                    $berasJumlahBaru->keterangan = $request->keterangan;
                    $berasJumlahBaru->save();
    
                    if ($newJumlahStock < 20) {
                        // Dispatch Telegram Message
                        dispatch(new SendTelegramMessageJob($newJumlahStock, $berasJumlahTerakhir, $jumlah_pengambilan, $newIdStockBerasJumlah));
    
                        $emails = DB::table('beras_pic_ga')
                                    ->where('email', 'LIKE', '%') 
                                    ->where('is_active', 'Y')     
                                    ->pluck('email');          
    
                        $emailTujuan = $emails->toArray();
    
                        // Dispatch email job
                        dispatch(new SendStockReminderEmailJumlahBeras($newJumlahStock, $emailTujuan));
                    }
                    return response()->json(['status' => 1, 'message' => 'Pengambilan stok berhasil.']);
                } else {
                    return response()->json(['status' => 2, 'error' => 'Stok tidak mencukupi untuk pengambilan ini.'], 400);
                }
            } else {
                return response()->json(['status' => 3, 'error' => 'Data stok tidak valid.'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 4, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    

public function formatExcelBufferStockBeras(Request $request)
{
    $tanggalAwal = $request->input('minDate');
    $tanggalAkhir = $request->input('maxDate');
    
    $dataStockBeras = BerasPengambilan::select([
        'tanggal_beras as Tanggal',
        'status as Status',
        'jumlah_pengambilan_sebelum', 
        'transaksi_masuk', 
        'transaksi_keluar', 
        'jumlah_pengambilan_sesudah', 
        'keterangan as Keterangan',
    ])
    ->whereBetween('tanggal_beras', [$tanggalAwal, $tanggalAkhir])
    ->orderBy('id_stock', 'desc')
    ->get();

    return Excel::download(new BufferStockBerasExport($dataStockBeras), 'buffer_stock_beras.xlsx');
}

public function getPengambilanBeras(Request $request) {
    $tanggalAwal = $request->input('minDate');
    $tanggalAkhir = $request->input('maxDate');

    // Buat query builder
    $query = BerasPengambilan::query();

    if ($tanggalAwal && $tanggalAkhir) {
        if ($tanggalAwal == $tanggalAkhir) {
            $query->where('tanggal_beras',  $tanggalAwal);
        } else {
            $query->whereBetween('tanggal_beras', [$tanggalAwal, $tanggalAkhir]);
        }
    }

    $dataBufferStockBeras = $query->orderBy('id_pengambilan', 'desc')->get();

    $response = [
        'data' => $dataBufferStockBeras,
        'status' => 'success',
        'code' => 200,
    ];

    return response()->json($response);
}

}
