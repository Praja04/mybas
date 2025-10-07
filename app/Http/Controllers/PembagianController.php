<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HR\Pembagian;
use App\Models\HR\PembagianKaryawan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportPembagianHR;

class PembagianController extends Controller
{
    public function index()
    {
        $pembagians = Pembagian::where('status', '!=', 'dihapus')->orderBy('created_at', 'desc')->get();
        return view('hr.pembagian.manage', [
            'pembagians' => $pembagians
        ]);
    }

    public function checkoutPage()
    {
        $pembagians = Pembagian::where('status', '!=', 'dihapus')->orderBy('created_at', 'desc')->get();
        return view('display.pembagian-checkout', [
            'pembagians' => $pembagians
        ]);
    }

    public function checkoutScan(Request $request)
    {
        $data = [];

        $id_card = (int)$request->id_card;

        // Get data dari secure access berdasarkan nomor kartu

        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'CREATEDON')
            ->where(['CARDNODEVICE' => $id_card])
            ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
            ->first();

        if ($user == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        $nik = $user->NIK;

        // Get foto nya
        $_user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'BARCODE', 'CARDNODEVICE', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where([
                'BARCODE' => $nik,
                // 'CARDNODEVICE' => $id_card
            ])
            ->orderByRaw('SUBSTR(NIK, 8) desc')
            ->first();

        $userArray = [];

        $userArray['nik']       = $_user->NIK;
        $userArray['nama']         = $_user->EMPNM;
        $userArray['dept']         = $_user->DEPTID;
        $userArray['foto']         = base64_encode($_user->FOTOBLOB);

        // Cek dulu apakah hari ini ada jadwal pembagian
        // $pembagian = Pembagian::where('tanggal_pembagian', date('Y-m-d'))->first();

        // if($pembagian == null) {
        //     return response()->json(['success' => 0, 'message' => 'Tidak ada jadwal pembagian hari ini']);
        // }

        // Cek apakah nik ini boleh ngambil

        $pembagian_karyawan = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)->where('nik', $nik)->first();
        if ($pembagian_karyawan == null) {
            return response()->json([
                'success' => 0,
                'message' => 'Maaf kamu ga bisa ambil, mohon hubungi tim HRD',
                'data' => $userArray
            ]);
        }

        // Cek kalau sudah pernah ngambil
        if ($pembagian_karyawan->status_ambil != 'sudah') {
            return response()->json([
                'success' => 0,
                'message' => 'Kamu belum secan, silahkan scan dulu',
                'data' => $userArray
            ]);
        }

        $userArray['keterangan']         = $pembagian_karyawan->keterangan;

        // Proses pengambilan

        $pic = $request->pic;

        $pembagian = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)->where('nik', $nik)->first();
        $pembagian->checkout_time = date('Y-m-d H:i:s');
        $pembagian->save();

        return response()->json([
            'success' => 1,
            'message' => 'Confirm pengambilan succeed',
            'data' => $userArray
        ]);
    }

    public function display($lokasi = '')
    {
        $pembagian = Pembagian::orderBy('created_at', 'desc')->get();
        return view('display.pembagian', [
            'pembagian' => $pembagian,
            'lokasi' => $lokasi
        ]);
    }


    public function displayScan(Request $request)
    {
        $data = [];

        $id_card = (int)$request->id_card;

        // Get data dari secure access berdasarkan nomor kartu
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'CREATEDON')
            ->where(['CARDNODEVICE' => $id_card])
            ->orderByRaw('CAST(NIK AS SIGNED) desc')
            ->first();

        if ($user == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        $nik = $user->NIK;

        // Get foto nya
        $_user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'BARCODE', 'CARDNODEVICE', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where([
                'BARCODE' => $nik,
                // 'CARDNODEVICE' => $id_card
            ])
            ->orderByRaw('CAST(NIK AS SIGNED) desc')
            ->first();

        $userArray = [];

        $userArray['nik']       = $_user->NIK;
        $userArray['nama']         = $_user->EMPNM;
        $userArray['dept']         = $_user->DEPTID;
        $userArray['foto']         = base64_encode($_user->FOTOBLOB);

        // Cek dulu apakah hari ini ada jadwal pembagian
        // $pembagian = Pembagian::where('tanggal_pembagian', date('Y-m-d'))->first();

        // if($pembagian == null) {
        //     return response()->json(['success' => 0, 'message' => 'Tidak ada jadwal pembagian hari ini']);
        // }

        // Cek apakah nik ini boleh ngambil

        $pembagian_karyawan = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)->where('nik', $nik)->where('lokasi_pembagian', $request->lokasi)->first();
        if ($pembagian_karyawan == null) {

            // Cek apakah ada pembagian di tempat lain
            $pembagian_tempat_lain = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)->where('nik', $nik)->orderBy('created_at', 'desc')->first();

            if ($pembagian_tempat_lain != null) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Tidak boleh ambil di sini, tapi di : ' . $pembagian_tempat_lain->lokasi_pembagian,
                    'data' => $userArray
                ]);
            }

            return response()->json([
                'success' => 0,
                'message' => 'Maaf kamu ga bisa ambil, mohon hubungi tim HRD',
                'data' => $userArray
            ]);
        }

        // Cek kalau sudah pernah ngambil
        if ($pembagian_karyawan->status_ambil == 'sudah') {
            return response()->json([
                'success' => 0,
                'message' => 'Kamu udah pernah ambil - ' . $pembagian_karyawan->waktu_ambil,
                'data' => $userArray
            ]);
        }

        $userArray['department']         = $pembagian_karyawan->department;
        $userArray['keterangan']         = $pembagian_karyawan->keterangan;

        // Proses pengambilan

        $pic = $request->pic;

        $pembagian = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)->where('nik', $nik)->first();
        $pembagian->pic = $pic;
        $pembagian->status_ambil = 'sudah';
        $pembagian->waktu_ambil = date('Y-m-d H:i:s');
        $pembagian->save();

        return response()->json([
            'success' => 1,
            'message' => 'Confirm pengambilan succeed',
            'data' => $userArray
        ]);
    }

    public function displayConfirm(Request $request)
    {
        $nik = $request->nik;
        $id_pembagian = $request->id_pembagian;
        $pic = $request->pic;

        $pembagian = PembagianKaryawan::where('id_pembagian', $id_pembagian)->where('nik', $nik)->first();
        $pembagian->pic = $pic;
        $pembagian->status_ambil = 'sudah';
        $pembagian->waktu_ambil = date('Y-m-d H:i:s');
        $pembagian->save();
        return response()->json(['success' => 1, 'message' => 'Confirm pengambilan succeed']);
    }

    public function create(Request $request)
    {
        // dd($request->all());
        $pembagian = new Pembagian;
        $pembagian->tanggal_pembagian = $request->tanggal;
        $pembagian->keterangan = $request->keterangan;
        $pembagian->status = 'dibuat';
        $pembagian->save();
        return response()->json(['success' => 1, 'message' => 'Pembagian created succeed']);
    }

    public function delete($id)
    {
        $pembagian = Pembagian::find($id);
        $pembagian->status = 'dihapus';
        $pembagian->save();

        return response()->json(['success' => 1, 'message' => 'Delete item succeed']);
    }

    public function exportPembagianKaryawan($id)
    {
        $pembagian = Pembagian::find($id);

        // if (!$pembagian) {
        //     return response()->json(['status' => 'error', 'message' => 'Pembagian not found']);
        // }

        return Excel::download(new ExportPembagianHR($id), 'pembagian_karyawan.xlsx');
    }
}
