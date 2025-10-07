<?php

namespace App\Http\Controllers;

use App\Imports\Ecafesedaap\OvertimeImport;
use Illuminate\Support\Facades\DB;
use Session;
use App\Imports\ExcelImport;
use App\Exports\PesananExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Validator;
use App\CateringbasPengecekanJumlahPesanan;
use App\pengambilanSampelCateringbas;
use App\pengirimCateringbas;
use App\ecafeSedaapBas;
use App\CateringbasMasterMakananUtama;
use App\CateringbasMasterMakananPendamping;
use Illuminate\Http\Request;
use App\PengecekanKendaraanCateringbas;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use PDF;

class CateringController extends Controller
{
    public function halamanUploadJumlahPesanan()
    {
        return view('hr.cateringbas.upload-pesanan.index');
    }

    public function pengecekanJumlahPesananCatering()
    {
        $pesanancateringbas = CateringbasPengecekanJumlahPesanan::all();
        return view('hr.cateringbas.kedatangan-lauk.index', [
            'data' => $pesanancateringbas
        ]);
    }

    public function getpesanan($id)
    {
        $pesanans = CateringbasPengecekanJumlahPesanan::find($id);
        return response()->json([
            'success' => 1,
            'data' => $pesanans,
            'message' => 'Get pesanans'
        ]);
    }

    // update multiple input value
    public function storeJumlahpesananCatering(Request $request)
    {
        $jumlahOrderUtama = 0;

        // Menghitung jumlah dari menu utama saja
        foreach ($request->input('nama_menu_utama') as $key => $namaMenuUtama) {
            $jumlahOrderUtama += (int)$request->input('qty_utama')[$key];

            if (!is_string($namaMenuUtama)) {
                return response()->json(['error' => 0, 'message' => 'Nama menu harus di input dengan huruf']);
            }
        }

        // Periksa apakah jumlah_order_bas kosong
        if (empty($request->jumlah_order_bas)) {
            return response()->json(['error' => 0, 'message' => 'Jumlah order bas kosong. Silahkan hubungi GA']);
        }

        // Perbandingan hanya berdasarkan menu utama
        // if ($request->jumlah_order_bas != $jumlahOrderUtama)
        if ($jumlahOrderUtama < $request->jumlah_order_bas) {
            $jumlahPesanan = new CateringbasPengecekanJumlahPesanan;
            $jumlahPesanan->id_transaksi = $request->id_transaksi;
            $jumlahPesanan->kategori_staff = $request->kategori_staff;
            $jumlahPesanan->shift = $request->shift;
            $jumlahPesanan->jumlah_order_bas = $request->jumlah_order_bas;
            $jumlahPesanan->jumlah_order = $jumlahOrderUtama;
            $jumlahPesanan->keterangan = 'tidak sesuai';

            $jumlahPesanan->save();

            // Proses untuk nama_menu_utama
            foreach ($request->input('nama_menu_utama') as $key => $namaMenuUtama) {
                CateringbasMasterMakananUtama::create([
                    'id_pesanan' => $jumlahPesanan->id,
                    'nama_menu' => $namaMenuUtama,
                    'qty' => $request->input('qty_utama')[$key],
                ]);
            }

            // Proses untuk nama_menu_pendamping
            foreach ($request->input('nama_menu_pendamping') as $key => $namaMenuPendamping) {
                CateringbasMasterMakananPendamping::create([
                    'id_pesanan' => $jumlahPesanan->id,
                    'nama_menu' => $namaMenuPendamping,
                    'qty' => $request->input('qty_pendamping')[$key],
                ]);
            }

            return response()->json(['success' => 2, 'message' => 'Jumlah pesanan yang datang tidak sesuai']);
        } else {
            $jumlahPesanan = new CateringbasPengecekanJumlahPesanan;
            $jumlahPesanan->id_transaksi = $request->id_transaksi;
            $jumlahPesanan->kategori_staff = $request->kategori_staff;
            $jumlahPesanan->shift = $request->shift;
            $jumlahPesanan->jumlah_order_bas = $request->jumlah_order_bas;
            $jumlahPesanan->jumlah_order = $jumlahOrderUtama;
            $jumlahPesanan->keterangan = 'sesuai';

            $jumlahPesanan->save();

            // Proses untuk nama_menu_utama
            foreach ($request->input('nama_menu_utama') as $key => $namaMenuUtama) {
                CateringbasMasterMakananUtama::create([
                    'id_pesanan' => $jumlahPesanan->id,
                    'nama_menu' => $namaMenuUtama,
                    'qty' => $request->input('qty_utama')[$key],
                ]);
            }

            // Proses untuk nama_menu_pendamping
            foreach ($request->input('nama_menu_pendamping') as $key => $namaMenuPendamping) {
                CateringbasMasterMakananPendamping::create([
                    'id_pesanan' => $jumlahPesanan->id,
                    'nama_menu' => $namaMenuPendamping,
                    'qty' => $request->input('qty_pendamping')[$key],
                ]);
            }

            return response()->json(['success' => 1, 'message' => 'Berhasil Upload Jumlah Pesanan']);
        }
    }


    public function getAllPesanan($id)
    {
        $dataPesanan = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id)->orderBy('id_transaksi', 'desc')->get();

        $combinedData = [];

        foreach ($dataPesanan as $orderPesanan) {
            $menuUtama = CateringbasMasterMakananUtama::where('id_pesanan', $orderPesanan->id)->pluck('nama_menu')->implode(', ');
            $menuPendamping = CateringbasMasterMakananPendamping::where('id_pesanan', $orderPesanan->id)->pluck('nama_menu')->implode(', ');

            $createdAt = date('Y-m-d', strtotime($orderPesanan->created_at));

            $pesanan = [
                'id' => $orderPesanan->id,
                'id_transaksi' => $orderPesanan->id_transaksi,
                'kategori_staff' => $orderPesanan->kategori_staff,
                'shift' => $orderPesanan->shift,
                'jumlah_order_bas' => $orderPesanan->jumlah_order_bas,
                'jumlah_order' => $orderPesanan->jumlah_order,
                'keterangan' => $orderPesanan->keterangan,
                'approved_at' => $orderPesanan->approved_at,
                'approved_by' => $orderPesanan->approved_by,
                'nama_menu_utama' => $menuUtama,
                'nama_menu_pendamping' => $menuPendamping,
                'created_at' => $createdAt,
            ];

            $combinedData[] = $pesanan;
        }

        $response = [
            'data' => $combinedData,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }


    public function editPesanan($id)
    {
        $data = CateringbasPengecekanJumlahPesanan::find($id);
        $menuUtama = CateringbasMasterMakananUtama::where('id_pesanan', $id)->get();
        $menuPendamping = CateringbasMasterMakananPendamping::where('id_pesanan', $id)->get();

        $combinedData = [
            'id' => $data->id,
            'id_transaksi' => $data->id_transaksi,
            'kategori_staff' => $data->kategori_staff,
            'jumlah_order' => $data->jumlah_order,
            'jumlah_order_bas' => $data->jumlah_order_bas,
            'keterangan' => $data->keterangan,
            'menu_utama' => $menuUtama,
            'menu_pendamping' => $menuPendamping,
        ];

        return response()->json($combinedData);
    }

    public function deleteMenuUtama($id)
    {
        CateringbasMasterMakananUtama::find($id)->delete();

        return response()->json(['success' => 1, 'message' => 'Berhasil Menghapus Menu Utama']);
    }

    public function deleteMenuPendamping($id)
    {
        CateringbasMasterMakananPendamping::find($id)->delete();

        return response()->json(['success' => 1, 'message' => 'Berhasil Menghapus Menu Pendamping']);
    }

    public function deletepesananCatering($id)
    {
        $data = CateringbasPengecekanJumlahPesanan::find($id);
        $data->delete();
        return response()->json(['success' => 1, 'message' => 'Berhasil Menghapus Pesanan']);
    }


    public function editpesananCatering(Request $request)
    {

        $jumlahPesanan = CateringbasPengecekanJumlahPesanan::find($request->edit_id);

        if ($jumlahPesanan) {
            // $jumlahPesanan->kategori_staff = $request->edit_kategori_staff;

            // Logika untuk menyimpan menu utama
            foreach ($request->input('menu_utama_nama', []) as $key => $namaMenu) {
                $qty = $request->input("menu_utama_qty.{$key}");

                CateringbasMasterMakananUtama::updateOrCreate(
                    ['id' => $request->input("menu_utama_id.{$key}")],
                    ['nama_menu' => $namaMenu, 'qty' => $qty]
                );
            }


            if ($request->has('menu_utama_nama_baru') && $request->has('menu_utama_qty_baru')) {
                foreach ($request->input('menu_utama_nama_baru') as $key => $namaMenu) {
                    $qty = $request->input("menu_utama_qty_baru.{$key}");

                    CateringbasMasterMakananUtama::create([
                        'id_pesanan' => $request->edit_id,
                        'nama_menu' => $namaMenu,
                        'qty' => $qty
                    ]);
                }
            }

            // Logika untuk menyimpan menu pendamping
            foreach ($request->input('menu_pendamping_nama', []) as $key => $namaMenu) {
                $qty = $request->input("menu_pendamping_qty.{$key}");

                CateringbasMasterMakananPendamping::updateOrCreate(
                    ['id' => $request->input("menu_pendamping_id.{$key}")],
                    ['nama_menu' => $namaMenu, 'qty' => $qty]
                );
            }

            if ($request->has('menu_pendamping_nama_baru') && $request->has('menu_pendamping_qty_baru')) {
                foreach ($request->input('menu_pendamping_nama_baru') as $key => $namaMenu) {
                    $qty = $request->input("menu_pendamping_qty_baru.{$key}");

                    CateringbasMasterMakananPendamping::create([
                        'id_pesanan' => $request->edit_id,
                        'nama_menu' => $namaMenu,
                        'qty' => $qty
                    ]);
                }
            }

            // Hitung dan update jumlah order
            $jumlah_order_utama = array_sum($request->input('menu_utama_qty', []));
            $jumlah_order_utama_baru = $request->has('menu_utama_qty_baru') ? array_sum($request->input('menu_utama_qty_baru')) : 0;
            $jumlah_order = $jumlah_order_utama + $jumlah_order_utama_baru;
            $jumlahPesanan->jumlah_order = $jumlah_order;
            $jumlahPesanan->keterangan = ($jumlah_order >= $jumlahPesanan->jumlah_order_bas) ? 'sesuai' : 'tidak sesuai';
            $jumlahPesanan->save();

            return response()->json(['success' => 1, 'message' => 'Update data pesanan berhasil']);
        } else {
            return response()->json(['success' => 0, 'message' => 'Pesanan tidak ditemukan']);
        }
    }



    // sampel catering
    public function pengambilanSampleCatering()
    {
        return view('hr.cateringbas.pengambilan-sampel-catering.index');
    }

    // pengecekan pengirim catering kendaraan
    public function cekPengirimCatering()
    {
        return view('hr.cateringbas.pengecekan-kendaraan.index');
    }

    private function generateIdtransaksi()
    {
        $latestId = pengirimCateringbas::orderBy('id_transaksi', 'desc')->first();

        if (!$latestId) {
            return 'K-001';
        }
        $latestIdNumber = (int)substr($latestId->id_transaksi, 2);
        $newIdNumber = $latestIdNumber + 1;
        $newId = 'K-' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);

        return $newId;
    }

    public function tambahpengirimCatering(Request $request)
    {
        $newId = $this->generateIdtransaksi();
        $pengirimcatering = new pengirimCateringbas;
        $pengirimcatering->id_transaksi = $newId;
        $pengirimcatering->tanggal = Carbon::now()->format('Y-m-d');
        $pengirimcatering->catering = $request->catering;
        $pengirimcatering->shift = $request->shift;
        $pengirimcatering->nama_petugas_security = auth()->user()->name;

        if ($request->hasFile('files')) {
            $fileName = time() . '.' . $request->file('files')[0]->getClientOriginalExtension();
            $fotoPath = $request->file('files')[0]->move('cateringbas/pengirim/', $fileName);
            $pengirimcatering->foto = $fileName;
        } else {
            return response()->json(['error' => 0, 'message' => 'Harap masukkan gambar kedatangan catering'], 400);
        }

        $pengirimcatering->save();

        return response()->json(['success' => 1, 'id_transaksi' => $pengirimcatering->id_transaksi, 'message' => 'Data Pengirim Catering Berhasil Disimpan']);
    }


    public function getdataPengirimCatering()
    {
        $dataPesanan = pengirimCateringbas::orWhere('status_cek_kendaraan', '!=', 'sudah')
            ->orWhere('status_cek_kedatangan', '!=', 'sudah')
            ->orWhere('status_pengambilan_sampel', '!=', 'sudah')
            ->orderBy('id', 'desc')
            ->get();

        $datacatering = [];

        foreach ($dataPesanan as $pengirimcatering) {
            $pesanan = [
                'id' => $pengirimcatering->id,
                'id_transaksi' => $pengirimcatering->id_transaksi,
                'catering' => $pengirimcatering->catering,
                'tanggal' => $pengirimcatering->tanggal,
                'foto' => asset('cateringbas/pengirim/' . $pengirimcatering->foto),
                'shift' => $pengirimcatering->shift,
                'status_cek_kendaraan' => $pengirimcatering->status_cek_kendaraan,
                'status_cek_kedatangan' => $pengirimcatering->status_cek_kedatangan,
                'status_pengambilan_sampel' => $pengirimcatering->status_pengambilan_sampel,
            ];

            $datacatering[] = $pesanan;
        }

        $response = [
            'data' => $datacatering,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }


    public function deletepengirimCatering($id)
    {
        $data = pengirimCateringbas::find($id);
        $data->delete();
        return response()->json(['success' => 1, 'message' => 'Berhasil Menghapus Pesanan']);
    }

    public function kuesionerPengirimBarang($id_transaksi)
    {
        $data = pengirimCateringbas::where('id_transaksi', $id_transaksi)->first();

        $kuesioner = PengecekanKendaraanCateringbas::where('id_transaksi', $id_transaksi)->first();

        return view('hr.cateringbas.pengecekan-kendaraan.kuesioner', compact('data', 'kuesioner'));
    }

    // redirect ke diganti 
    public function storeKuesionerKendaraan(Request $request)
    {
        $data = $request->validate([
            'fisik_dinding_sebelah_kanan' => 'required|numeric|min:0',
            'fisik_dinding_sebelah_kiri' => 'required|numeric|min:0',
            'fisik_kondisi_atap' => 'required|numeric|min:0',
            'fisik_kondisi_lantai' => 'required|numeric|min:0',
            'kebersihan_dinding_sebelah_kanan' => 'required|numeric|min:0',
            'kebersihan_dinding_sebelah_kiri' => 'required|numeric|min:0',
            'kebersihan_kondisi_atap' => 'required|numeric|min:0',
            'kebersihan_kondisi_lantai' => 'required|numeric|min:0',
            'ditemukan_barang_lain_diluar_kebutuhan_catering' => 'required|numeric|min:0',
            'saat_penerimaan_pintu_keadaan_tertutup' => 'required|numeric|min:0',
            'saat_penerimaan_pintu_keadaan_terkunci' => 'required|numeric|min:0',
            'box_makanan_dalam_keadaan_tertutup' => 'required|numeric|min:0',
        ]);

        $transaksi = pengirimCateringbas::where('id_transaksi', $request['id_transaksi'])->first();

        $quesioner = PengecekanKendaraanCateringbas::create($request->all());
        $transaksi->update([
            'status_cek_kendaraan' => "menunggu approval",
        ]);

        Session::flash('success', 'Pengecekan Kendaraan Berhasil Dikirim!');

        return redirect()->route('cateringbas.pengecekaan-kendaraan');
    }

    public function masterKedatanganCatering()
    {
        return view('hr.cateringbas.kedatangan-lauk.index');
    }

    public function dashboardkedatangancatering($id_transaksi_pesanan)
    {
        $data = PengirimCateringbas::where('id_transaksi', $id_transaksi_pesanan)->first();

        // Ambil tanggal dari data pengirim catering
        $tanggal_pengiriman = $data->tanggal;


        // Hitung totalAmount berdasarkan tanggal_pengiriman
        $totalAmount = DB::table('ecafesedaapbas')
            ->whereDate('tanggal', $tanggal_pengiriman)
            ->whereIn('tanggal', function ($query) use ($tanggal_pengiriman) {
                $query->select('tanggal')
                    ->from('pengirim_cateringbas')
                    ->whereDate('tanggal', $tanggal_pengiriman);
            })
            ->sum('jumlah');

        // Ambil data lainnya
        $ecafesedaapbasData = Ecafesedaapbas::where('tanggal', $tanggal_pengiriman)->get();
        $jumlahpesanan = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_pesanan)->first();
        $shift = $data->shift ?? '';

        // Kirim semua data yang diperlukan ke view
        return view('hr.cateringbas.kedatangan-lauk.pengirim-pesanan', compact('data', 'jumlahpesanan', 'shift', 'ecafesedaapbasData', 'tanggal_pengiriman', 'totalAmount'));
    }


    // pengambilan sample pesanan
    public function dashboardSampleCatering($id_transaksi_sample)
    {
        // Mengambil data pengirim
        $data = pengirimCateringbas::where('id_transaksi', $id_transaksi_sample)->first();

        $pesananList = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_sample)->get();

        $MenuMakanan = [];
        $menuPengambilans = [];

        foreach ($pesananList as $pesanan) {
            $menuUtama = CateringbasMasterMakananUtama::where('id_pesanan', $pesanan->id)->get();
            $menuPendamping = CateringbasMasterMakananPendamping::where('id_pesanan', $pesanan->id)->get();

            foreach ($menuUtama as $mu) {
                $MenuMakanan[] = [
                    'id' => $mu->id,
                    'jenis_menu' => 'utama',
                    'nama_menu' => $mu->nama_menu,
                    'qty' => $mu->qty,
                    'kategori_staff' => $pesanan->kategori_staff,
                    'pengambilan' => $mu->pengambilan
                ];

                if ($mu->pengambilan == 1) {
                    $menuPengambilans[] = $MenuMakanan[count($MenuMakanan) - 1];
                }
            }

            foreach ($menuPendamping as $mp) {
                $MenuMakanan[] = [
                    'id' => $mp->id,
                    'jenis_menu' => 'pendamping',
                    'nama_menu' => $mp->nama_menu,
                    'qty' => $mp->qty,
                    'kategori_staff' => $pesanan->kategori_staff,
                    'pengambilan' => $mp->pengambilan
                ];

                if ($mp->pengambilan == 1) {
                    $menuPengambilans[] = $MenuMakanan[count($MenuMakanan) - 1];
                }
            }
        }

        $id_transaksi_pengambilan_sampel = pengambilanSampelCateringbas::where('id_transaksi', $id_transaksi_sample)->value('id_transaksi');

        return view('hr.cateringbas.pengambilan-sampel-catering.pengambilan-sampel', compact('data', 'MenuMakanan', 'pesananList', 'id_transaksi_pengambilan_sampel', 'menuPengambilans'));
    }


    public function getAllSample($id)
    {
        $dataSample = pengambilanSampelCateringbas::where('id_transaksi', $id)->orderBy('id_transaksi', 'desc')->get();

        $combinedData = [];

        foreach ($dataSample as $orderpesanan) {

            $pesanan = [
                'id' => $orderpesanan->id,
                'id_transaksi' => $orderpesanan->id_transaksi,
                'tanggal_jam_masuk' => $orderpesanan->tanggal_jam_masuk,
                'tanggal_jam_keluar' => $orderpesanan->tanggal_jam_keluar,
                'foto_before_1' => '',
                'foto_before_2' => '',
                'foto_after_1' => '',
                'foto_after_2' => '',
                'keterangan' => $orderpesanan->keterangan,
                'keterangan_menu' => $orderpesanan->keterangan_menu,
                'keterangan_menu_keluar' => $orderpesanan->keterangan_menu_keluar,
                'kategori_staff' => $orderpesanan->kategori_staff,
                'status_approval' => $orderpesanan->status_approval,
                'approved_at' => $orderpesanan->approved_at,
                'approved_by' => $orderpesanan->approved_by,
                'masa_simpan' => $orderpesanan->masa_simpan,
            ];

            if (is_string($orderpesanan->foto_before)) {
                $fotoBeforeArray = json_decode($orderpesanan->foto_before, true);

                if (is_array($fotoBeforeArray)) {
                    $pesanan['foto_before_1'] = isset($fotoBeforeArray['gambar1']) ? asset("cateringbas/sampel/before/{$fotoBeforeArray['gambar1']}") : '';
                    $pesanan['foto_before_2'] = isset($fotoBeforeArray['gambar2']) ? asset("cateringbas/sampel/before/{$fotoBeforeArray['gambar2']}") : '';
                }
            }

            if (is_string($orderpesanan->foto_after)) {
                $fotoAfterArray = json_decode($orderpesanan->foto_after, true);

                if (is_array($fotoAfterArray)) {
                    $pesanan['foto_after_1'] = isset($fotoAfterArray['gambar1']) ? asset("cateringbas/sampel/after/{$fotoAfterArray['gambar1']}") : '';
                    $pesanan['foto_after_2'] = isset($fotoAfterArray['gambar2']) ? asset("cateringbas/sampel/after/{$fotoAfterArray['gambar2']}") : '';
                }
            }

            $combinedData[] = $pesanan;
        }

        $response = [
            'data' => $combinedData,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }


    // private function untuk tanggal sekarang dan jam sekarang
    private function getTanggalJamMasuk()
    {
        return Carbon::now();
    }

    // private function untuk tanggal besok dan jam besok
    private function getTanggalJamKeluar($tanggalJamMasuk)
    {
        return Carbon::parse($tanggalJamMasuk)->addDay();
    }

    public function storeSampelCatering(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (strpos($key, 'pengambilan-utama') === 0) {
                $id_pengambilan_utama = substr($key, strlen('pengambilan-utama-'));

                $menuUtama = CateringbasMasterMakananUtama::where('id', $id_pengambilan_utama)->first();

                $menuUtama->update(['pengambilan' => $value]);
            }
        }
        foreach ($data as $key => $value) {
            if (strpos($key, 'pengambilan-pendamping') === 0) {
                $id_pengambilan_pendamping = substr($key, strlen('pengambilan-pendamping-'));

                $menuPendamping = CateringbasMasterMakananPendamping::where('id', $id_pengambilan_pendamping)->first();

                $menuPendamping->update(['pengambilan' => $value]);
            }
        }
        // Process foto_before1
        $fotobefore1 = time() . '1.' . $request->file('files_before1')[0]->getClientOriginalExtension();
        $fotoPathbefore1 = $request->file('files_before1')[0]->move('cateringbas/sampel/before', $fotobefore1);

        // Process foto_before2
        $fotobefore2 = time() . '2.' . $request->file('files_before2')[0]->getClientOriginalExtension();
        $fotoPathbefore2 = $request->file('files_before2')[0]->move('cateringbas/sampel/before', $fotobefore2);

        $gambar = json_encode(['gambar1' => $fotobefore1, 'gambar2' => $fotobefore2]);


        // Create or update the pengambilanSampelCateringbas record
        $jumlahPesanan = new pengambilanSampelCateringbas;
        $jumlahPesanan->id_transaksi = $request->id_transaksi;
        $jumlahPesanan->tanggal_jam_masuk = $this->getTanggalJamMasuk();
        $jumlahPesanan->nama_petugas_kantin =  auth()->user()->name;
        // $jumlahPesanan->keterangan_menu = $request->keterangan_menu;
        // $jumlahPesanan->baik = $request->baik;

        // $jumlahPesanan->kategori_staff = $request->kategori_staff;
        $jumlahPesanan->foto_before = $gambar;
        $jumlahPesanan->save();

        return response()->json(['success' => 1, 'message' => 'Berhasil Melakukan Pengambilan Sampel']);
    }


    public function editSampelCatering(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (strpos($key, 'penilaian-utama') === 0) {
                $id_penilaian_utama = substr($key, strlen('penilaian-utama-'));

                $menuUtama = CateringbasMasterMakananUtama::where('id', $id_penilaian_utama)->first();

                $menuUtama->update(['baik' => $value]);
            }
        }
        foreach ($data as $key => $value) {
            if (strpos($key, 'penilaian-pendamping') === 0) {
                $id_penilaian_pendamping = substr($key, strlen('penilaian-pendamping-'));

                $menuPendamping = CateringbasMasterMakananPendamping::where('id', $id_penilaian_pendamping)->first();

                $menuPendamping->update(['baik' => $value]);
            }
        }

        // cari id pengambilan catering
        $jumlahPesanan = pengambilanSampelCateringbas::find($request->id);

        // proses foto after 1
        if ($request->hasFile('files_after1')) {
            $fotoafter1 = time() . '1.' . $request->file('files_after1')[0]->getClientOriginalExtension();
            $fotoPathafter1 = $request->file('files_after1')[0]->move('cateringbas/sampel/after', $fotoafter1);
        } else {
            $fotoafter1 = null;
        }

        // proses foto after 2
        if ($request->hasFile('files_after2')) {
            $fotoafter2 = time() . '2.' . $request->file('files_after2')[0]->getClientOriginalExtension();
            $fotoPathafter2 = $request->file('files_after2')[0]->move('cateringbas/sampel/after', $fotoafter2);
        } else {
            $fotoafter2 = null;
        }

        $gambar = json_encode(['gambar1' => $fotoafter1, 'gambar2' => $fotoafter2]);

        $tanggalJamMasuk = Carbon::parse($jumlahPesanan->tanggal_jam_masuk);
        $tanggalJamKeluar = Carbon::now();
        // Menghitung perbedaan waktu
        $diff = $tanggalJamKeluar->diff($tanggalJamMasuk);

        // Menentukan masa simpan dalam format "x jam y menit z detik"
        $masaSimpan = ($diff->days * 24 + $diff->h) . ' jam ' .
            $diff->i . ' menit ' .
            $diff->s . ' detik';

        if ($diff->invert == 0) {
            // Format output (opsional)
            // echo "{$diff->d} hari, {$diff->h} jam, {$diff->i} menit, dan {$diff->s} detik";
        }

        // Update record
        $jumlahPesanan->id_transaksi = $request->id_transaksi;
        $jumlahPesanan->tanggal_jam_keluar = $tanggalJamKeluar;
        $jumlahPesanan->keterangan_menu_keluar = $request->keterangan_menu_keluar;
        $jumlahPesanan->foto_after = $gambar;
        $jumlahPesanan->masa_simpan = $masaSimpan;
        $jumlahPesanan->save();

        // Mengembalikan response
        return response()->json(['success' => 1, 'message' => 'Berhasil Memberi Penilaian Sampel']);
    }


    public function deleteSampel($id)
    {
        $data = pengambilanSampelCateringbas::find($id);
        $data->delete();
        return response()->json(['success' => 1, 'message' => 'Berhasil Menghapus Sampel']);
    }

    public function reportCateringPengecekanKendaraan()
    {
        $pengirimCateringBas = pengirimCateringbas::latest('id')->first();
        return view('hr.cateringbas.reporting-approver.index', [
            'data' => $pengirimCateringBas
        ]);
    }

    public function ReportPesanan($id_transaksi_report_pesanan)
    {
        $data = pengirimCateringbas::where('id_transaksi', $id_transaksi_report_pesanan)->first();

        $jumlahpesanan = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_report_pesanan)->first();

        return view('hr.cateringbas.reporting-approver.cateringbas-cek-pesanan', compact('data', 'jumlahpesanan'));
    }

    public function ReportSampel($id_transaksi_report_sampel)
    {
        // Dapatkan data transaksi dan pesanan
        $data = PengirimCateringbas::where('id_transaksi', $id_transaksi_report_sampel)->first();
        $pesananList = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_report_sampel)->get();

        $MenuMakanan = [];

        foreach ($pesananList as $pesanan) {
            $menuUtama = CateringbasMasterMakananUtama::where('id_pesanan', $pesanan->id)->get();
            $menuPendamping = CateringbasMasterMakananPendamping::where('id_pesanan', $pesanan->id)->get();

            foreach ($menuUtama as $mu) {
                $MenuMakanan[$pesanan->kategori_staff]['utama'][] = [
                    'id' => $mu->id,
                    'nama_menu' => $mu->nama_menu,
                    'baik' => $mu->baik,
                    'pengambilan' => $mu->pengambilan
                ];
            }

            foreach ($menuPendamping as $mp) {
                $MenuMakanan[$pesanan->kategori_staff]['pendamping'][] = [
                    'id' => $mp->id,
                    'nama_menu' => $mp->nama_menu,
                    'baik' => $mp->baik,
                    'pengambilan' => $mp->pengambilan
                ];
            }
        }

        $id_transaksi_pengambilan_sampel = PengambilanSampelCateringbas::where('id_transaksi', $id_transaksi_report_sampel)->value('id_transaksi');
        $jumlahPesanan = count($pesananList);

        return view('hr.cateringbas.reporting-approver.cateringbas-cek-sampel', [
            'data' => $data,
            'MenuMakanan' => $MenuMakanan,
            'jumlahPesanan' => $jumlahPesanan,
            'id_transaksi_pengambilan_sampel' => $id_transaksi_pengambilan_sampel
        ]);
    }




    public function ReportKendaraan($id_transaksi_report_kendaraan)
    {
        $data = pengirimCateringbas::where('id_transaksi', $id_transaksi_report_kendaraan)->first();

        $jumlahpesanan = PengecekanKendaraanCateringbas::where('id_transaksi', $id_transaksi_report_kendaraan)->first();

        return view('hr.cateringbas.reporting-approver.cateringbas-cek-kendaraan', compact('data', 'jumlahpesanan'));
    }

    public function getAllPengecekanKendaraan($id)
    {
        $dataPesanan = PengecekanKendaraanCateringbas::where('id_transaksi', $id)->get();

        $response = [
            'data' => $dataPesanan,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    public function getAllKendaraan($id)
    {
        $dokumenData = pengirimCateringbas::where('id_transaksi', $id)->get();

        $combinedData = [];

        foreach ($dokumenData as $dokumen) {

            $dataDokumen = [
                'id' => $dokumen->id,
                'id_transaksi' => $dokumen->id_transaksi,
                'foto' => asset('cateringbas/pengirim/' . $dokumen->foto),
                'tanggal' => $dokumen->tanggal,
                'catering' => $dokumen->catering,
                'shift' => $dokumen->shift,
            ];

            $combinedData[] = $dataDokumen;
        }

        $response = [
            'data' => $combinedData,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    public function ApproveReportingPesanan($id_transaksi_approve_pesanan)
    {
        $now = now();

        PengirimCateringbas::where('id_transaksi', $id_transaksi_approve_pesanan)->update([
            "status_cek_kedatangan" => "sudah",
            "approval_cek_pesanan" => "Y",
            "approval_cek_pesanan_at" => $now,
            "approval_cek_pesanan_by" => auth()->user()->name,
        ]);

        CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_approve_pesanan)->update([
            "status_approval" => "Y",
            "approved_at" => $now,
            "approved_by" => auth()->user()->name,
        ]);

        Session::flash('info', 'Data Berhasil di Approve!');
        return back();
    }

    public function ApproveReportingSample($id_transaksi_approve_sampel)
    {
        $now = now();

        PengirimCateringbas::where('id_transaksi', $id_transaksi_approve_sampel)->update([
            "status_pengambilan_sampel" => "sudah",
            "approval_cek_sampel" => "Y",
            "approval_cek_sampel_at" => $now,
            "approval_cek_sampel_by" => auth()->user()->name,
        ]);

        pengambilanSampelCateringbas::where('id_transaksi', $id_transaksi_approve_sampel)->update([
            "status_approval" => "Y",
            "approved_at" => $now,
            "approved_by" => auth()->user()->name,
        ]);

        Session::flash('info', 'Data Berhasil di Approve!');
        return back();
    }

    // tambahkan approve all untuk semua transaksi
    public function ApproveAll()
    {
        $now = now();
        $user_name = auth()->user()->name;
    
        // Dapatkan semua ID transaksi yang memenuhi kriteria
        $transaksiIds = PengirimCateringbas::query()
            ->where('status_cek_kendaraan', 'menunggu approval')
            ->where('status_cek_kedatangan', 'menunggu approval')
            ->where('status_pengambilan_sampel', 'menunggu approval')
            ->pluck('id_transaksi');
    
        if ($transaksiIds->isEmpty()) {
            Session::flash('error', 'Tidak ada transaksi yang memenuhi kriteria!');
            return back();
        }
    
        PengirimCateringbas::query()
            ->whereIn('id_transaksi', $transaksiIds)
            ->update([
                "status_cek_kedatangan" => "sudah",
                "approval_cek_pesanan" => "Y",
                "approval_cek_pesanan_at" => $now,
                "approval_cek_pesanan_by" => $user_name,
                "status_pengambilan_sampel" => "sudah",
                "approval_cek_sampel" => "Y",
                "approval_cek_sampel_at" => $now,
                "approval_cek_sampel_by" => $user_name,
                "status_cek_kendaraan" => "sudah",
                "approval_cek_kendaraan" => "Y",
                "approval_cek_kendaraan_at" => $now,
                "approval_cek_kendaraan_by" => $user_name,
            ]);
    
        CateringbasPengecekanJumlahPesanan::query()
            ->whereIn('id_transaksi', $transaksiIds)
            ->update([
                "status_approval" => "Y",
                "approved_at" => $now,
                "approved_by" => $user_name,
            ]);
    
        pengambilanSampelCateringbas::query()
            ->whereIn('id_transaksi', $transaksiIds)
            ->update([
                "status_approval" => "Y",
                "approved_at" => $now,
                "approved_by" => $user_name,
            ]);
    
        PengecekanKendaraanCateringbas::query()
            ->whereIn('id_transaksi', $transaksiIds)
            ->update([
                "status_approval" => "Y",
                "approved_at" => $now,
                "approved_by" => $user_name,
            ]);
    
        Session::flash('info', 'Transaksi yang memenuhi kriteria berhasil di-approve!');
        return back();
    }
    



    public function ApprovePengecekanKendaraan($id_transaksi_approve_kendaraan)
    {
        $now = now();

        PengirimCateringbas::where('id_transaksi', $id_transaksi_approve_kendaraan)->update([
            "status_cek_kendaraan" => "sudah",
            "approval_cek_kendaraan" => "Y",
            "approval_cek_kendaraan_at" => $now,
            "approval_cek_kendaraan_by" => auth()->user()->name,
        ]);

        PengecekanKendaraanCateringbas::where('id_transaksi', $id_transaksi_approve_kendaraan)->update([
            "status_approval" => "Y",
            "approved_at" => $now,
            "approved_by" => auth()->user()->name,
        ]);

        Session::flash('info', 'Data Berhasil di Approve!');
        return back();
    }

    public function kirimKedatanganCatering($id_transaksi_kirim_catering)
    {
        $idTransaksiTanpaPesanan = DB::table('pengirim_cateringbas')
            ->leftJoin('cateringbas_pengecekan_jumlah_pesanan', 'pengirim_cateringbas.id_transaksi', '=', 'cateringbas_pengecekan_jumlah_pesanan.id_transaksi')
            ->whereNull('cateringbas_pengecekan_jumlah_pesanan.id_transaksi')
            ->where('pengirim_cateringbas.id_transaksi', $id_transaksi_kirim_catering)
            ->select('pengirim_cateringbas.id_transaksi')
            ->first();

        if ($idTransaksiTanpaPesanan) {
            session()->flash('error', 'Silahkan tambah kedatangan pesanan dulu');
            return back();
        }

        // Cek apakah "status_cek_kedatangan" sudah "menunggu approval"
        $pengirimCatering = PengirimCateringbas::where('id_transaksi', $id_transaksi_kirim_catering)
            ->where('status_cek_kedatangan', '!=', 'menunggu approval')
            ->first();

        if ($pengirimCatering) {
            // Update the status and nama_petugas_kantin in PengirimCateringbas model
            $pengirimCatering->update([
                "status_cek_kedatangan" => "menunggu approval",
                "nama_petugas_kantin_pesanan" => auth()->user()->name,
            ]);

            // Update juga anama petugas kantin di model jumlah pesanan
            CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_kirim_catering)
                ->update([
                    "nama_petugas_kantin" => auth()->user()->name,
                ]);

            session()->flash('info', 'Data Berhasil Dikirim!');
        } else {
            session()->flash('error', 'Anda sudah mengirim kedatangan pesanan catering');
        }

        return back();
    }


    public function kirimPengambilanSampel($id_transaksi_kirim_catering)
    {
        $idTransaksiTanpaPesanan = DB::table('pengirim_cateringbas')
            ->leftJoin('pengambilan_sampel_cateringbas', 'pengirim_cateringbas.id_transaksi', '=', 'pengambilan_sampel_cateringbas.id_transaksi')
            ->where('pengirim_cateringbas.id_transaksi', $id_transaksi_kirim_catering)
            ->where(function ($query) {
                $query->whereNull('pengambilan_sampel_cateringbas.id_transaksi')
                    ->orWhereNull('pengambilan_sampel_cateringbas.tanggal_jam_keluar')
                    ->orWhereNull('pengambilan_sampel_cateringbas.masa_simpan')
                    ->orWhereNull('pengambilan_sampel_cateringbas.foto_after')
                    ->orWhereNull('pengambilan_sampel_cateringbas.keterangan_menu_keluar');
            })
            ->select('pengirim_cateringbas.id_transaksi')
            ->first();

        if ($idTransaksiTanpaPesanan) {
            session()->flash('error', 'Silahkan Tambah Data Pengambilan Sampel');
            return back();
        }

        // Cek apakah "status_cek_kedatangan" sudah "menunggu approval"
        $pengirimCatering = PengirimCateringbas::where('id_transaksi', $id_transaksi_kirim_catering)
            ->where('status_pengambilan_sampel', '!=', 'menunggu approval')
            ->first();

        if ($pengirimCatering) {
            $pengirimCatering->update([
                "status_pengambilan_sampel" => "menunggu approval",
                "nama_petugas_kantin_sampel" => auth()->user()->name,
            ]);

            pengambilanSampelCateringbas::where('id_transaksi', $id_transaksi_kirim_catering)
                ->update([
                    "nama_petugas_kantin" => auth()->user()->name,
                ]);

            session()->flash('info', 'Data Berhasil Dikirim!');
        } else {
            session()->flash('error', 'Anda sudah mengirim Data Pengambilan Sampel');
        }
        return back();
    }

    public function getDetailDateCatering()
    {
        session()->flash('info', 'Halaman sedang dalam perbaikan. Silakan coba lagi nanti.');

        return redirect()->back();
    }

    public function getdataPengirimCateringDetail()
    {
        $dataPesanan = pengirimCateringbas::all();

        $datacatering = [];

        foreach ($dataPesanan as $pengirimcatering) {

            $pesanan = [
                'id' => $pengirimcatering->id,
                'id_transaksi' => $pengirimcatering->id_transaksi,
                'catering' => $pengirimcatering->catering,
                'tanggal' => $pengirimcatering->tanggal,
                'foto' => asset('cateringbas/pengirim/' . $pengirimcatering->foto),
                'shift' => $pengirimcatering->shift,
                'status_cek_kendaraan' => $pengirimcatering->status_cek_kendaraan,
                'status_cek_kedatangan' => $pengirimcatering->status_cek_kedatangan,
                'status_pengambilan_sampel' => $pengirimcatering->status_pengambilan_sampel,
            ];

            $datacatering[] = $pesanan;
        }

        $response = [
            'data' => $datacatering,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    public function getReportingGACatering()
    {
        $dataPesanan = pengirimCateringbas::query()
            ->where('status_cek_kendaraan', '!=', 'sudah')
            ->orWhere('status_cek_kedatangan', '!=', 'sudah')
            ->orWhere('status_pengambilan_sampel', '!=', 'sudah')
            ->orderBy('id_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $datacatering = [];

        foreach ($dataPesanan as $pengirimcatering) {

            $pesanan = [
                'id' => $pengirimcatering->id,
                'id_transaksi' => $pengirimcatering->id_transaksi,
                'catering' => $pengirimcatering->catering,
                'tanggal' => $pengirimcatering->tanggal,
                'foto' => asset('cateringbas/pengirim/' . $pengirimcatering->foto),
                'shift' => $pengirimcatering->shift,
                'nama_petugas_security' => $pengirimcatering->nama_petugas_security,
                'nama_petugas_kantin_pesanan' => $pengirimcatering->nama_petugas_kantin_pesanan,
                'nama_petugas_kantin_sampel' => $pengirimcatering->nama_petugas_kantin_sampel,
                'status_cek_kendaraan' => $pengirimcatering->status_cek_kendaraan,
                'status_cek_kedatangan' => $pengirimcatering->status_cek_kedatangan,
                'status_pengambilan_sampel' => $pengirimcatering->status_pengambilan_sampel,
            ];

            $datacatering[] = $pesanan;
        }

        $response = [
            'data' => $datacatering,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    public function getReportingUserCatering()
    {
        $dataPesanan = pengirimCateringbas::orWhere('status_cek_kendaraan', '!=', 'sudah')
        ->orWhere('status_cek_kedatangan', '!=', 'sudah')
        ->orWhere('status_pengambilan_sampel', '!=', 'sudah')
        ->orderBy('id', 'desc')
        ->get();

        $datacatering = [];

        foreach ($dataPesanan as $pengirimcatering) {

            $pesanan = [
                'id' => $pengirimcatering->id,
                'id_transaksi' => $pengirimcatering->id_transaksi,
                'catering' => $pengirimcatering->catering,
                'tanggal' => $pengirimcatering->tanggal,
                'foto' => asset('cateringbas/pengirim/' . $pengirimcatering->foto),
                'shift' => $pengirimcatering->shift,
                'nama_petugas_security' => $pengirimcatering->nama_petugas_security,
                'nama_petugas_kantin_pesanan' => $pengirimcatering->nama_petugas_kantin_pesanan,
                'nama_petugas_kantin_sampel' => $pengirimcatering->nama_petugas_kantin_sampel,
                'status_cek_kendaraan' => $pengirimcatering->status_cek_kendaraan,
                'status_cek_kedatangan' => $pengirimcatering->status_cek_kedatangan,
                'status_pengambilan_sampel' => $pengirimcatering->status_pengambilan_sampel,
            ];

            $datacatering[] = $pesanan;
        }

        $response = [
            'data' => $datacatering,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }

    public function getReportingGACateringDetail()
    {
        $dataPesanan = pengirimCateringbas::all();

        $datacatering = [];

        foreach ($dataPesanan as $pengirimcatering) {

            $pesanan = [
                'id' => $pengirimcatering->id,
                'id_transaksi' => $pengirimcatering->id_transaksi,
                'catering' => $pengirimcatering->catering,
                'tanggal' => $pengirimcatering->tanggal,
                'foto' => asset('cateringbas/pengirim/' . $pengirimcatering->foto),
                'shift' => $pengirimcatering->shift,
                'nama_petugas_security' => $pengirimcatering->nama_petugas_security,
                'nama_petugas_kantin_pesanan' => $pengirimcatering->nama_petugas_kantin_pesanan,
                'nama_petugas_kantin_sampel' => $pengirimcatering->nama_petugas_kantin_sampel,
                'status_cek_kendaraan' => $pengirimcatering->status_cek_kendaraan,
                'status_cek_kedatangan' => $pengirimcatering->status_cek_kedatangan,
                'status_pengambilan_sampel' => $pengirimcatering->status_pengambilan_sampel,
            ];

            $datacatering[] = $pesanan;
        }

        $response = [
            'data' => $datacatering,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }


    public function getapprovalreporting()
    {
        return view('hr.cateringbas.reporting-approver.detail-reporting-catering');
    }

    // public function getApprovalDetail()
    // {
    //     $where = [];
    //     if (isset($_GET['tanggal'])) {
    //         $where[] = ['tanggal', $_GET['tanggal']];
    //     }
    //     $where[] = ['status', 1];
    //     $master = DB::table('hr_masuk_hari_libur')
    //         ->where($where)
    //         ->where('nik_approver', Auth::user()->username)
    //         ->get();

    //     $master = $master->map(function ($item) {
    //         $karyawan = DB::table('hr_masuk_hari_libur_karyawan')
    //             ->where('id_mhl', $item->id_mhl)
    //             ->get();
    //         $item->jumlah_karyawan = $karyawan->count();
    //         $item->jumlah_scan = $karyawan->where('is_scan', 'Y')->count();
    //         $item->tidak_scan = $karyawan->where('is_scan', 'N')->count();

    //         // Ambil informasi created_by dari hr_masuk_hari_libur
    //         $item->created_by = $item->created_by;

    //         return $item;
    //     });

    //     return view('hr.masukharilibur.approver.index', compact('master'));
    // }
    public function ReportPesananDetail($id_transaksi_report_pesanan_detail)
    {
        $data = pengirimCateringbas::where('id_transaksi', $id_transaksi_report_pesanan_detail)->first();

        $jumlahpesanan = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_report_pesanan_detail)->first();

        return view('hr.cateringbas.reporting-approver.detail-cateringbas-cek-pesanan', compact('data', 'jumlahpesanan'));
    }

    public function ReportSampelDetail($id_transaksi_report_sampel_detail)
    {
        $data = PengirimCateringbas::where('id_transaksi', $id_transaksi_report_sampel_detail)->first();
        $pesananList = CateringbasPengecekanJumlahPesanan::where('id_transaksi', $id_transaksi_report_sampel_detail)->get();

        $MenuMakanan = [];

        foreach ($pesananList as $pesanan) {
            $menuUtama = CateringbasMasterMakananUtama::where('id_pesanan', $pesanan->id)->get();
            $menuPendamping = CateringbasMasterMakananPendamping::where('id_pesanan', $pesanan->id)->get();

            foreach ($menuUtama as $mu) {
                $MenuMakanan[$pesanan->kategori_staff]['utama'][] = [
                    'id' => $mu->id,
                    'nama_menu' => $mu->nama_menu,
                    'baik' => $mu->baik,
                    'pengambilan' => $mu->pengambilan
                ];
            }

            foreach ($menuPendamping as $mp) {
                $MenuMakanan[$pesanan->kategori_staff]['pendamping'][] = [
                    'id' => $mp->id,
                    'nama_menu' => $mp->nama_menu,
                    'baik' => $mp->baik,
                    'pengambilan' => $mp->pengambilan
                ];
            }
        }

        $id_transaksi_pengambilan_sampel = PengambilanSampelCateringbas::where('id_transaksi', $id_transaksi_report_sampel_detail)->value('id_transaksi');
        $jumlahPesanan = count($pesananList);

        return view('hr.cateringbas.reporting-approver.detail-cateringbas-cek-sampel', [
            'data' => $data,
            'MenuMakanan' => $MenuMakanan,
            'jumlahPesanan' => $jumlahPesanan,
            'id_transaksi_pengambilan_sampel' => $id_transaksi_pengambilan_sampel
        ]);
    }

    public function ReportKendaraanDetail($id_transaksi_report_kendaraan_detail)
    {
        $data = pengirimCateringbas::where('id_transaksi', $id_transaksi_report_kendaraan_detail)->first();

        $jumlahpesanan = PengecekanKendaraanCateringbas::where('id_transaksi', $id_transaksi_report_kendaraan_detail)->first();

        return view('hr.cateringbas.reporting-approver.detail-cateringbas-cek-kendaraan', compact('data', 'jumlahpesanan'));
    }

    public function getQtyAll(Request $request)
    {
        // Mengubah format tanggal menjadi 'Y-m-d'
        $tanggal = Carbon::createFromFormat('Y-m-d', $request->input('tanggal'))->format('Y-m-d');

        // Menghitung total jumlah berdasarkan tanggal
        $totalAmount = ecafeSedaapBas::whereDate('tanggal', $tanggal)->where('kategori', $request->kategori_staff)->where('shift', $request->shift)->sum('jumlah');

        // Mengembalikan respons JSON
        return response()->json($totalAmount);
    }

    public function reportingMingguanBeras()
    {
        $pesanancateringbas = ecafeSedaapBas::all();
        return view('hr.cateringbas.cetak-report-catering.index', [
            'data' => $pesanancateringbas
        ]);
    }


    public function exportreportingMingguanBeras(Request $request)
    {
        $dataExists = ecafeSedaapBas::whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])->exists();

        if (!$dataExists) {
            Session::flash('info', 'Data untuk tanggal yang dipilih kosong.');
            return back();
        }

        return Excel::download(new PesananExport($request->tanggal_awal, $request->tanggal_akhir), 'pesanan.xlsx');
    }

    public function getDatareportingMingguanCatering(Request $request)
    {
        $validated = $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal'
        ]);
    
        $tanggalAwal = Carbon::parse($validated['tanggal_awal'])->format('Y-m-d');
        $tanggalAkhir = Carbon::parse($validated['tanggal_akhir'])->format('Y-m-d');
    
        $reportData = EcafeSedaapBas::selectRaw('tanggal, kategori, 
                    SUM(CASE WHEN shift = 1 THEN jumlah ELSE 0 END) as shift1_sum, 
                    SUM(CASE WHEN shift = 2 THEN jumlah ELSE 0 END) as shift2_sum, 
                    SUM(CASE WHEN shift = 3 THEN jumlah ELSE 0 END) as shift3_sum')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->groupBy('tanggal', 'kategori')
            ->orderBy('tanggal', 'asc')
            ->orderBy('kategori', 'asc')
            ->get();
    
        return response()->json($reportData);
    }    

    public function exportPdf($tanggal_awal, $tanggal_akhir)
    {
        $tanggalAwal = $tanggal_awal->input('tanggal_awal');
        $tanggalAkhir = $tanggal_akhir->input('tanggal_akhir');
    
        $reportData = EcafeSedaapBas::selectRaw('tanggal, kategori, 
                        SUM(CASE WHEN shift = 1 THEN jumlah ELSE 0 END) as shift1_sum, 
                        SUM(CASE WHEN shift = 2 THEN jumlah ELSE 0 END) as shift2_sum, 
                        SUM(CASE WHEN shift = 3 THEN jumlah ELSE 0 END) as shift3_sum')
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->groupBy('tanggal', 'kategori')
                ->orderBy('tanggal', 'asc')
                ->orderBy('kategori', 'asc')
                ->get();
    
        // Render data ke dalam file PDF
        $pdf = PDF::loadView('hr.cateringbas.cetak-report-catering.report-catering-pdf', ['reportData' => $reportData]);
        return $pdf->download('laporan_mingguan.pdf');
    }
}
