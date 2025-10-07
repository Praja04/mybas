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
use App\Mail\BufferStockReminder;
use App\Jobs\SendBufferStockReminderEmail;
use App\Jobs\sendPemakaianMessageJob;

class PemakaianBerasController extends Controller
{
    public function homePemakaian()
    {
        $pengambilanBeras = BerasPengambilan::all();
        $satuanBerat = BerasSatuanBerat::all();
        $berasPemakaian = BerasPemakaian::paginate(5);
        return view('hr.cateringbas.pengambilan-beras.pemakaian-beras', compact('pengambilanBeras', 'satuanBerat', 'berasPemakaian'));
    }

    // versi lama pembatasan >= 2 
    // public function penguranganStockBeras(Request $request)
    // {
    //     // Format tanggal
    //     $currentDate = Carbon::now();
    //     $formattedDate = $currentDate->format('Y-m-d');

    //     // Validasi jika sudah melebihi batas pengambilan ambil di tanggal berikutnya
    //     $jumlahPenarikan = BerasPemakaian::where('tanggal', $formattedDate)
    //         ->where('shift', $request->shift)
    //         ->count();

    //     if ($jumlahPenarikan >= 2) {
    //         session()->flash('error', 'Batas pengambilan beras hari ini sudah tercapai');
    //         return redirect()->back()->with('delay', 3);
    //     }

    //     // Progres pembuatan angka menjadi desimal
    //     $lastRow = BerasPemakaian::latest('id')->first();

    //     // Cek apakah tanggal entri terakhir sama dengan tanggal saat ini
    //     if ($lastRow && $lastRow->tanggal == $formattedDate) {
    //         $jumlah_pemakaian = $request->jumlah_pemakaian + $lastRow->jumlah_pemakaian;
    //     } else {
    //         $jumlah_pemakaian = $request->jumlah_pemakaian;
    //     }

    //     // Batasi jumlah pengambilan
    //     $maxJumlahPengambilan = 2;

    //     if ($jumlah_pemakaian > $maxJumlahPengambilan) {
    //         session()->flash('error', 'Jumlah pengambilan tidak boleh lebih dari ' . $maxJumlahPengambilan . ' sak');
    //         return redirect()->back()->with('delay', 3);
    //     } else {
    //         $stockberas = new BerasPemakaian();
    //         $stockberas->id_stock = $request->id_stock;
    //         $stockberas->tanggal = $formattedDate;
    //         $stockberas->jumlah_pemakaian = $jumlah_pemakaian;
    //         $stockberas->shift = $request->shift;
    //         $stockberas->status = 'in';
    //         $stockberas->keterangan = $request->keterangan;

    //         $transaksi = BerasPengambilan::where('id_stock', $request->id_stock)->first();
    //         $transaksi->update([
    //             "status" => "in",
    //         ]);

    //         if ($stockberas) {
    //             $stockberas->save();
    //             session()->flash('success', 'Berhasil mengurangi stock');
    //         } else {
    //             session()->flash('error', 'BerasJumlah tidak ditemukan');
    //         }

    //         return redirect()->back()->with('delay', 3);
    //     }
    // }

    // function baru
    private function generateNewIdStock($lastIdStock)
    {
        $tanggalSekarang = Carbon::now()->format('dmY');
        if (!$lastIdStock) {
            return 'BERAS-' . $tanggalSekarang . '-1';
        }

        $parts = explode('-', $lastIdStock);
        $nomorUrut = intval(end($parts)) + 1;
    
        $formattedNomorUrut = str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);
    
        return "BERAS-" . $parts[1] . "-" . $formattedNomorUrut;
    }

    private function generateIdPemakaian()
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


    public function penguranganStockBeras(Request $request)
    {
        // Gunakan id_stock dari request, bukan generateIdPemakaian baru
        $idStock = $request->id_stock;
        $id_pengambilan = $this->generateIdPemakaian();
        $currentDate = Carbon::now()->format('Y-m-d H:i:s');
        $tanggalBerasTanpaWaktu = Carbon::now()->format('Y-m-d');
    
        // Dapatkan row terakhir dari tabel BerasPemakaian
        $lastBerasPemakaians = BerasPemakaian::latest()->first();
        $jumlah_pemakaian_sebelum = $lastBerasPemakaians ? $lastBerasPemakaians->jumlah_pemakaian_sesudah : 0;
        $jumlah_pemakaian = $request->jumlah_pemakaian;
        $jumlah_pemakaian_sesudah = $jumlah_pemakaian_sebelum + $jumlah_pemakaian;
    
        $berasJumlahTerakhir = BerasPengambilan::where('id_stock', '<>', '')
            ->orderBy('id_stock', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($berasJumlahTerakhir) {
            if ($jumlah_pemakaian > $berasJumlahTerakhir->jumlah_pengambilan_sesudah) {
                session()->flash('error', 'Melebihi jumlah pemakaian buffer stock.');
                return back();
            }
    
            $newJumlahPengambilan = $berasJumlahTerakhir->jumlah_pengambilan_sesudah - $jumlah_pemakaian;
            $newIdStock = $this->generateNewIdStock($berasJumlahTerakhir->id_stock);
    
            $berasJumlahPengambilan = new BerasPengambilan();
            $berasJumlahPengambilan->id_stock = $newIdStock;
            $berasJumlahPengambilan->id_pengambilan = $id_pengambilan;
            $berasJumlahPengambilan->tanggal = $currentDate;
            $berasJumlahPengambilan->tanggal_beras = $tanggalBerasTanpaWaktu;
            $berasJumlahPengambilan->transaksi_keluar = $jumlah_pemakaian;
            $berasJumlahPengambilan->jumlah_pengambilan_sebelum = $berasJumlahTerakhir->jumlah_pengambilan_sesudah;
            $berasJumlahPengambilan->jumlah_pengambilan_sesudah = $newJumlahPengambilan;
            $berasJumlahPengambilan->keterangan = $request->keterangan;
            $berasJumlahPengambilan->status = 'out';
            $berasJumlahPengambilan->satuan_berat = 'liter';
            $berasJumlahPengambilan->save();
    
            session()->flash('success', 'Berhasil mengurangi stock dan membuat catatan baru.');
        } else {
            session()->flash('error', 'Data stock tidak ditemukan.');
            return back();
        }
    
        // Hanya jika kondisi sebelumnya tidak terpenuhi, maka Buat BerasPemakaian baru
        $stockberas = new BerasPemakaian();
        $stockberas->id_stock = $idStock;
        $stockberas->tanggal = $currentDate;
        $stockberas->tanggal_beras = $tanggalBerasTanpaWaktu;
        $stockberas->jumlah_pemakaian_sebelum = $jumlah_pemakaian_sebelum;
        $stockberas->jumlah_pemakaian = $jumlah_pemakaian;
        $stockberas->jumlah_pemakaian_sesudah = $jumlah_pemakaian_sesudah;
        $stockberas->Shift = $request->shift;
        $stockberas->keterangan = $request->keterangan;
        $stockberas->transaksi_masuk = $jumlah_pemakaian;
        $stockberas->status = 'in';
        $stockberas->satuan_berat = 'liter';
        $stockberas->save();

        if ($newJumlahPengambilan < 20) {
            dispatch(new sendPemakaianMessageJob($newJumlahPengambilan, $berasJumlahTerakhir, $jumlah_pemakaian, $newIdStock));
            dispatch(new SendBufferStockReminderEmail($newJumlahPengambilan));
        }
    
        return redirect()->back()->with('delay', 3);
    }
}
