<?php

namespace App\Http\Controllers\KedatanganBeras;

use App\Imports\Ecafesedaap\OvertimeImport;
use Illuminate\Support\Facades\DB;
use Session;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Carbon\Carbon;
use App\BerasPemakaian;
use App\BerasPengambilan;
use App\BerasSatuanBerat;
use App\BerasStock;
use App\BerasJumlah;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Exports\StockBerasExport;
use Illuminate\Support\Facades\Cache;

class KedatanganBerasController extends Controller
{
    public function dashboard()
    {
        $berasStockData = BerasStock::with('pengirimanStock')->get();
        // testing relasi
        // return $berasStockData;
        return view('hr.cateringbas.pengambilan-beras.master-beras', compact('berasStockData'));
    }

    public function index()
    {
        $kedatanganBeras = BerasStock::all();
        $satuanBeratOptions = BerasSatuanBerat::all();

        return view('hr.cateringbas.pengambilan-beras.kedatangan-stock-beras', compact('kedatanganBeras', 'satuanBeratOptions'));
    }

    public function jumlahberas(Request $request)
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

        return view('hr.cateringbas.pengambilan-beras.jumlah-beras', compact('jumlahStock', 'latestBerasJumlah', 'transaksiMasukCount', 'transaksiKeluarCount'));
    }

    public function getJumlahBeras(Request $request)
    {
        $tanggalAwal = $request->input('minDate');
        $tanggalAkhir = $request->input('maxDate');
    
        $query = BerasJumlah::query();
    
        if ($tanggalAwal && $tanggalAkhir) {
            if ($tanggalAwal == $tanggalAkhir) {
                $query->where('tanggal_beras',  $tanggalAwal);
            } else {
                $query->whereBetween('tanggal_beras', [$tanggalAwal, $tanggalAkhir]);
            }
        }
    
        $query->orderBy('id_stock', 'desc');
        $dataStockBeras = $query->get();
    
        $response = [
            'data' => $dataStockBeras,
            'status' => 'success',
            'code' => 200,
        ];
    
        return response()->json($response);
    }

    public function formatExcelBeras(Request $request)
    {
        $tanggalAwal = $request->input('minDate');
        $tanggalAkhir = $request->input('maxDate');
        
        $dataStockBeras = BerasJumlah::select([
            'tanggal_beras as Tanggal',
            'status as Status',
            'jumlah_stock', 
            'transaksi_masuk', 
            'transaksi_keluar', 
            'jumlah_stock_sesudah', 
            'keterangan as Keterangan',
        ])
        ->whereBetween('tanggal_beras', [$tanggalAwal, $tanggalAkhir])
        ->orderBy('id_stock', 'desc')
        ->get();

        return Excel::download(new StockBerasExport($dataStockBeras), 'stock_beras.xlsx');
    }

    private function generateIdStockBeras($tableName)
    {
        if ($tableName == 'BerasStock') {
            $latestRecord = BerasStock::orderBy('id_stock', 'desc')->first();
        } elseif ($tableName == 'BerasJumlah') {
            $latestRecord = BerasJumlah::orderBy('id_stock', 'desc')->first();
        } else {
            throw new Exception("data table tidak ditemukan");
        }
    
        $currentDate = Carbon::now()->format('dmY');
    
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
    

    public function storeStockberas(Request $request)
    {
    $newId = $this->generateIdStockBeras('BerasStock');
    $currentDate = Carbon::now()->format('Y-m-d H:i:s');
    $tanggalBerasTanpaWaktu = Carbon::now()->format('Y-m-d');

    $stockberas = new BerasStock();
    $stockberas->id_stock = $newId;
    $stockberas->kedatangan_stock = $request->kedatangan_stock;
    $stockberas->qty_kedatangan_stock = $request->kedatangan_stock;
    $stockberas->satuan_berat = $request->satuan_berat;
    $stockberas->tanggal = $currentDate;
    $stockberas->tanggal_beras = $tanggalBerasTanpaWaktu;
    $stockberas->save();

    // Menghitung jumlah stock
    $lastBerasJumlah = Cache::rememberForever('last_beras_jumlah', function () {
        return BerasJumlah::where('id_stock', '<>', '')
            ->orderBy('id_stock', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    });

    $jumlah_stock = $lastBerasJumlah ? $lastBerasJumlah->jumlah_stock_sesudah + $request->kedatangan_stock : $request->kedatangan_stock;

    $newIdBerasJumlah = $this->generateIdStockBeras('BerasJumlah');
    $BerasJumlah = new BerasJumlah();
    $BerasJumlah->id_stock = $newIdBerasJumlah;
    $BerasJumlah->tanggal = $currentDate;
    $BerasJumlah->tanggal_beras = $tanggalBerasTanpaWaktu;
    $BerasJumlah->jumlah_stock = $lastBerasJumlah ? $lastBerasJumlah->jumlah_stock_sesudah : 0;
    $BerasJumlah->jumlah_stock_sesudah = $jumlah_stock;
    $BerasJumlah->transaksi_masuk = $request->kedatangan_stock;
    $BerasJumlah->active = 'Y';
    $BerasJumlah->status = 'in';
    $BerasJumlah->satuan_berat = 'sak';
    $BerasJumlah->save();

    // Hapus cache jika ada perubahan pada data
    Cache::forget('last_beras_jumlah');

    return response()->json(['success' => 1, 'message' => 'Beras stock berhasil ditambahkan']);
    }
    

    // public function storeStockberas(Request $request)
    // {
    //     $newId = $this->generateIdStockBeras();
    //     $currentDate = Carbon::now();
    //     // $formattedDate = $currentDate->format('Y-m-d');

    //     $LastBeras = BerasStock::latest()->first();

    //     if ($LastBeras) {
    //         $stockberas = new BerasStock;
    //         $stockberas->id_stock = $newId;
    //         $stockberas->kedatangan_stock = $LastBeras->kedatangan_stock + $request->kedatangan_stock;
    //         $stockberas->satuan_berat = $request->satuan_berat;
    //         $stockberas->tanggal = $currentDate;
    //         $stockberas->save();

    //         $BerasJumlah = new BerasJumlah;
    //         $BerasJumlah->id_stock = $stockberas->id_stock;
    //         $BerasJumlah->tanggal = $stockberas->tanggal;
    //         $BerasJumlah->jumlah_stock = $stockberas->kedatangan_stock;
    //         $BerasJumlah->active = 'Y';
    //         $BerasJumlah->status = 'pending';
    //         $BerasJumlah->satuan_berat = 'sak';
    //         $BerasJumlah->save();
    //     } else {
    //         $stockberas = new BerasStock;
    //         $stockberas->id_stock = $newId;
    //         $stockberas->kedatangan_stock = $request->kedatangan_stock;
    //         $stockberas->satuan_berat = $request->satuan_berat;
    //         $stockberas->tanggal = $currentDate;
    //         $stockberas->save();

    //         $BerasJumlah = new BerasJumlah;
    //         $BerasJumlah->id_stock = $stockberas->id_stock;
    //         $BerasJumlah->tanggal = $stockberas->tanggal;
    //         $BerasJumlah->jumlah_stock = $stockberas->kedatangan_stock;
    //         $BerasJumlah->active = 'Y';
    //         $BerasJumlah->status = 'pending';
    //         $BerasJumlah->satuan_berat = 'sak';
    //         $BerasJumlah->save();
    //     }

    //     // return $LastBeras;

    //     // $existingStock = BerasStock::where('tanggal', $formattedDate)->first();

    //     // if ($existingStock) {
    //     //     $existingStock->update([
    //     //         'kedatangan_stock' => $existingStock->kedatangan_stock + $request->kedatangan_stock,
    //     //         'satuan_berat' => $request->satuan_berat,
    //     //     ]);

    //     //     // Update related BerasJumlah records
    //     //     $existingStock->stock()->update([
    //     //         'jumlah_stock' => DB::raw('jumlah_stock + ' . $request->kedatangan_stock),
    //     //         'satuan_berat' => $request->satuan_berat,
    //     //         'active' => 'y',
    //     //     ]);
    //     // } else {
    //     //     $stockberas = new BerasStock;
    //     //     $stockberas->id_stock = $newId;
    //     //     $stockberas->kedatangan_stock = $request->kedatangan_stock;
    //     //     $stockberas->satuan_berat = $request->satuan_berat;
    //     //     $stockberas->tanggal = $currentDate;
    //     //     $stockberas->save();

    //     //     // Ambil id_stock yang baru saja disetel
    //     //     $newlySetIdStock = $stockberas->id_stock;

    //     //     // create juga table beras jumlah   
    //     //     $stockberas->stock()->create([
    //     //         'id_stock' => $newlySetIdStock,
    //     //         'tanggal' => $currentDate,
    //     //         'jumlah_stock' => $request->kedatangan_stock,
    //     //         'satuan_berat' => $request->satuan_berat,
    //     //         'status' => 'pending',
    //     //         'active' => 'y',
    //     //     ]);
    //     // }

    //     return response()->json(['success' => 1, 'message' => 'beras stock berhasil ditambahkan']);
    // }

    public function getDatastock()
    {
        // Retrieve all BerasStock entries
        $dataStock = BerasStock::all();

        $totalDataStock = [];

        foreach ($dataStock as $kedatanganStockBeras) {

            $pesanan = [
                'id' => $kedatanganStockBeras->id,
                'id_stock' => $kedatanganStockBeras->id_stock,
                // Format the 'tanggal' field to 'Y-m-d'
                'tanggal' => Carbon::parse($kedatanganStockBeras->tanggal)->format('Y-m-d H:i:s'),
                'qty_kedatangan_stock' => $kedatanganStockBeras->qty_kedatangan_stock,
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


    public function deletestock($id)
    {
        $data = BerasStock::find($id);
        $data->delete();
        return response()->json(['success' => 1, 'message' => 'Berhasil Menghapus stock']);
    }

    public function tambahSatuanBerat(Request $request)
    {
        $request->validate([
            'satuan_berat' => 'required'
        ]);

        // cek jika satuan_berat beras sudah ada
        $cek = BerasStock::where('satuan_berat', $request->satuan_berat)->first();
        if ($cek != null) {
            return response(['success' => 0, 'message' => 'satuan sudah ada']);
        }

        $satuanBerat = new BerasStock;
        $satuanBerat->satuan_berat = $request->satuan_berat;
        $satuanBerat->save();

        return response(['success' => 1, 'message' => 'Berhasil menambah satuan berat', 'data' => $satuanBerat]);
    }

    public function grafikKedatanganBeras(Request $request)
    {
        $chartData = BerasPengambilan::all();
        return view('hr.cateringbas.pengambilan-beras-reporting.laporan-stock-beras', compact('chartData'));
    }

    public function getJumlahBerasPetugas(Request $request)
    {
    $tanggalAwal = $request->input('minDate');
    $tanggalAkhir = $request->input('maxDate');

    $query = BerasJumlah::query();

    // Menambahkan filter untuk memastikan id_stock tidak kosong
    $query->where('id_stock', '<>', '');

    if ($tanggalAwal && $tanggalAkhir) {
        if ($tanggalAwal == $tanggalAkhir) {
            $query->where('tanggal_beras',  $tanggalAwal);
        } else {
            $query->whereBetween('tanggal_beras', [$tanggalAwal, $tanggalAkhir]);
        }
    }

    // Mengurutkan berdasarkan id_stock dan created_at
    $query->orderBy('id_stock', 'desc')
        ->orderBy('created_at', 'desc');

    $dataStockBeras = $query->get();

    $response = [
        'data' => $dataStockBeras,
        'status' => 'success',
        'code' => 200,
    ];

    return response()->json($response);
}




    // public function grafikKedatanganBeras()
    // {
    //     $grafik = BerasStock::select(DB::raw("CAST(SUM(kedatangan_stock) as int ) as kedatangan_stock "))
    //         ->groupBy("DB:raw(Month(created_at))")
    //         ->pluck('kedatangan_stock');

    //     $tanggal = BerasStock::select(DB::raw("MONTHNAME(created_at)"))
    //         ->groupBy("DB:raw(Month(created_at))")
    //         ->pluck('kedatangan_stock');
    // }
}
