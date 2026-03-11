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
        $id_card = (int)$request->id_card;

        // Ambil data karyawan dari server
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where('CARDNODEVICE', $id_card)
            ->orderByRaw('CAST(NIK AS SIGNED) desc')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $nik = $user->NIK;

        // Cek apakah sudah scan
        $cek = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)
            ->where('nik', $nik)
            ->first();

        if ($cek) {
            return response()->json([
                'success' => 0,
                'message' => $user->EMPNM . ' sudah pernah scan pada ' . $cek->waktu_ambil,
                'data' => [
                    'nik' => $user->NIK,
                    'nama' => $user->EMPNM,
                    'dept' => $user->DEPTID
                ]
            ]);
        }

        // Simpan data scan
        $scan = new PembagianKaryawan;
        $scan->id_pembagian = $request->id_pembagian;
        $scan->nik = $user->NIK;
        $scan->nama = $user->EMPNM;
        $scan->department = $user->DEPTID;

        $scan->lokasi_pembagian = 'GA';   // default lokasi
        $scan->pic = $request->pic;       // dari blade

        $scan->status_ambil = 'sudah';
        $scan->waktu_ambil = now();

        $scan->save();
        return response()->json([
            'success' => 1,
            'message' => 'Scan berhasil',
            'data' => [
                'nik' => $user->NIK,
                'nama' => $user->EMPNM,
                'dept' => $user->DEPTID
            ]
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
           $id_card = (int)$request->id_card;

        // Ambil data karyawan dari server
        $user = DB::connection('192.168.178.44-admin')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'DEPTID')
            ->where('CARDNODEVICE', $id_card)
            ->orderByRaw('CAST(NIK AS SIGNED) desc')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $nik = $user->NIK;

        // Cek apakah sudah scan
        $cek = PembagianKaryawan::where('id_pembagian', $request->id_pembagian)
            ->where('nik', $nik)
            ->first();

        if ($cek) {
            return response()->json([
                'success' => 0,
                'message' => $user->EMPNM . ' sudah pernah scan pada ' . $cek->waktu_ambil,
                'data' => [
                    'nik' => $user->NIK,
                    'nama' => $user->EMPNM,
                    'dept' => $user->DEPTID
                ]
            ]);
        }

        // Simpan data scan
        $scan = new PembagianKaryawan;
        $scan->id_pembagian = $request->id_pembagian;
        $scan->nik = $user->NIK;
        $scan->nama = $user->EMPNM;
        $scan->department = $user->DEPTID;

        $scan->lokasi_pembagian = $request->lokasi;   // default lokasi
        $scan->pic = $request->pic;       // dari blade

        $scan->status_ambil = 'sudah';
        $scan->waktu_ambil = now();

        $scan->save();
        return response()->json([
            'success' => 1,
            'message' => 'Scan berhasil',
            'data' => [
                'nik' => $user->NIK,
                'nama' => $user->EMPNM,
                'dept' => $user->DEPTID
            ]
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
