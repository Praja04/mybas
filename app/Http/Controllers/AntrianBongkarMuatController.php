<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntrianBongkarMuat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;

class AntrianBongkarMuatController extends Controller
{
    public function kiosk()
    {
        return view('antrian-bongkar-muat.kiosk');
    }

    public function generateTiket(Request $request)
    {
        $kategori = $request->kategori ?? 'bongkar_muat';
        
        // Map kategori to prefix
        $prefixes = [
            'bongkar_muat' => 'BM-',
            'tamu' => 'TMU-',
            'tkbm' => 'TKBM-'
        ];
        
        $prefix = $prefixes[$kategori] ?? 'BM-';

        $antrianTerakhir = AntrianBongkarMuat::where('kategori', $kategori)
            ->orderBy('id', 'desc')
            ->first();
        
        $nomorBerikutnya = 1;
        if ($antrianTerakhir) {
            // Extract numeric part regardless of prefix length
            $nomorTerakhir = (int) preg_replace('/[^0-9]/', '', $antrianTerakhir->nomor_antrian);
            $nomorBerikutnya = $nomorTerakhir + 1;
        }

        $nomorFormat = $prefix . str_pad($nomorBerikutnya, 3, '0', STR_PAD_LEFT);

        $fotoPath = null;
        if ($request->has('foto') && !empty($request->foto)) {
            $imageParts = explode(";base64,", $request->foto);
            if (count($imageParts) >= 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                if (count($imageTypeAux) >= 2) {
                    $imageType = $imageTypeAux[1];
                    $imageBase64 = base64_decode($imageParts[1]);
                    $fileName = $nomorFormat . '_' . time() . '.' . $imageType;
                    // Menyimpan ke storage/app/public/kiosk_photos
                    \Storage::disk('public')->put('kiosk_photos/' . $fileName, $imageBase64);
                    $fotoPath = 'storage/kiosk_photos/' . $fileName;
                }
            }
        }

        $antrian = AntrianBongkarMuat::create([
            'nomor_antrian' => $nomorFormat,
            'kategori' => $kategori,
            'status' => 'waiting',
            'foto' => $fotoPath
        ]);

        $printWarning = null;
        try {
            $printerPath = env('PRINTER_ANTRIAN_PATH', 'smb://127.0.0.1/PrinterAntrian');
            $connector = new WindowsPrintConnector($printerPath);
            $printer = new Printer($connector);

            // Reset printer
            $printer->initialize();

            // Desain Struk
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            
            // Header
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(true);
            $printer->text("PT BUMI ALAM SEGAR\n");
            
            $kategoriText = '';
            if($kategori == 'bongkar_muat') $kategoriText = 'Bongkar Muat';
            else if($kategori == 'tamu') $kategoriText = 'Tamu';
            else $kategoriText = 'TKBM';
            
            $printer->text("Antrian " . $kategoriText . "\n");
            $printer->text("--------------------------------\n");
            
            // Nomor Antrian
            $printer->setEmphasis(true);
            $printer->setTextSize(4, 4); // Ubah ke 3,3 jika 4,4 masih kepotong
            $printer->text("\n" . $nomorFormat . "\n\n");
            
            $printer->setTextSize(1, 1);
            $printer->text("--------------------------------\n");
            $printer->setEmphasis(false);
            
            // Footer
            $printer->text(date('d/m/Y H:i:s') . "\n");
            $printer->text("Silakan tunggu\n");
            $printer->text("nomor Anda dipanggil\n");
            $printer->text("oleh petugas Security\n");
            
            $printer->feed(3);
            $printer->cut();
            $printer->close();

        } catch (\Exception $e) {
            \Log::error('Print Error: ' . $e->getMessage());
            $printWarning = "Gagal mencetak ke printer: " . $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'data' => $antrian,
            'message' => 'Tiket berhasil dibuat',
            'print_warning' => $printWarning
        ]);
    }

    public function monitor()
    {
        return view('antrian-bongkar-muat.monitor');
    }

    public function getMonitorData()
    {
        $categories = ['bongkar_muat', 'tamu', 'tkbm'];
        $data = [];

        foreach ($categories as $kategori) {
            $current = AntrianBongkarMuat::where('kategori', $kategori)
                ->whereIn('status', ['called', 'serving'])
                ->orderBy('updated_at', 'desc')
                ->first();

            $next = AntrianBongkarMuat::where('kategori', $kategori)
                ->where('status', 'waiting')
                ->orderBy('id', 'asc')
                ->limit(3)
                ->get();

            $data[$kategori] = [
                'current' => $current,
                'next' => $next
            ];
        }

        // Used by frontend to detect when a new call or re-call happens
        $latestCall = AntrianBongkarMuat::whereNotNull('waktu_dipanggil')
            ->orderBy('waktu_dipanggil', 'desc')
            ->first();

        return response()->json([
            'categories' => $data,
            'latest_call' => $latestCall
        ]);
    }

    public function dashboard()
    {
        // Bongkar Muat
        $bmMenunggu = AntrianBongkarMuat::where('kategori', 'bongkar_muat')->where('status', 'waiting')->count();
        $bmActive = AntrianBongkarMuat::where('kategori', 'bongkar_muat')->where('status', 'serving')->first();
        
        // Tamu
        $tamuMenunggu = AntrianBongkarMuat::where('kategori', 'tamu')->where('status', 'waiting')->count();
        $tamuActive = AntrianBongkarMuat::where('kategori', 'tamu')->where('status', 'serving')->first();
        
        // TKBM
        $tkbmMenunggu = AntrianBongkarMuat::where('kategori', 'tkbm')->where('status', 'waiting')->count();
        $tkbmActive = AntrianBongkarMuat::where('kategori', 'tkbm')->where('status', 'serving')->first();

        $bmList = AntrianBongkarMuat::where('kategori', 'bongkar_muat')->orderBy('id', 'asc')->get();
        $tamuList = AntrianBongkarMuat::where('kategori', 'tamu')->orderBy('id', 'asc')->get();
        $tkbmList = AntrianBongkarMuat::where('kategori', 'tkbm')->orderBy('id', 'asc')->get();

        $permissions = $this->myPermissions()->toArray();

        return view('antrian-bongkar-muat.dashboard', compact(
            'bmMenunggu', 'bmActive', 
            'tamuMenunggu', 'tamuActive', 
            'tkbmMenunggu', 'tkbmActive', 
            'bmList', 'tamuList', 'tkbmList',
            'permissions'
        ));
    }

    public function panggilBerikutnya(Request $request)
    {
        $kategori = $request->kategori;

        // 1. Jika ada kategori spesifik, selesaikan yang sedang aktif di kategori tersebut
        if ($kategori) {
            $active = AntrianBongkarMuat::where('kategori', $kategori)
                ->where('status', 'serving')
                ->first();
                
            if ($active) {
                $active->update([
                    'status' => 'completed',
                    'waktu_selesai' => Carbon::now()
                ]);
            }
        }

        // 2. Cari antrian berikutnya yang menunggu
        $query = AntrianBongkarMuat::where('status', 'waiting');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $berikutnya = $query->orderBy('id', 'asc')->first();

        if ($berikutnya) {
            $berikutnya->update([
                'status' => 'serving',
                'waktu_dipanggil' => Carbon::now()
            ]);
            return redirect()->back()->with('success', 'Berhasil memanggil ' . $berikutnya->nomor_antrian);
        }

        return redirect()->back()->with('info', 'Tidak ada antrian ' . ($kategori ? str_replace('_', ' ', $kategori) : '') . ' yang menunggu.');
    }

    public function panggilUlang($id)
    {
        $antrian = AntrianBongkarMuat::findOrFail($id);
        $antrian->update([
            'waktu_dipanggil' => Carbon::now()
        ]);
        
        return redirect()->back()->with('success', 'Memanggil ulang ' . $antrian->nomor_antrian);
    }

    public function lewati($id)
    {
        $antrian = AntrianBongkarMuat::findOrFail($id);
        $antrian->update(['status' => 'skipped']);
        
        return redirect()->back()->with('success', 'Melewati ' . $antrian->nomor_antrian);
    }

    public function layani($id)
    {
        $antrian = AntrianBongkarMuat::findOrFail($id);
        $antrian->update([
            'status' => 'serving'
        ]);
        
        return redirect()->back()->with('success', 'Memproses ' . $antrian->nomor_antrian);
    }

    public function selesai($id)
    {
        $antrian = AntrianBongkarMuat::findOrFail($id);
        $antrian->update([
            'status' => 'completed',
            'waktu_selesai' => Carbon::now()
        ]);
        
        return redirect()->back()->with('success', 'Menyelesaikan ' . $antrian->nomor_antrian);
    }

    public function resetAntrian()
    {
        AntrianBongkarMuat::query()->delete();
        return redirect()->back()->with('success', 'Antrian berhasil di-reset (Data tetap tersimpan di database).');
    }
}
