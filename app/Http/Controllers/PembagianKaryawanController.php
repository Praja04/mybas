<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HR\PembagianKaryawan;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PembagianKaryawanImport;
use Illuminate\Support\Facades\Auth;

class PembagianKaryawanController extends Controller
{
    public function get($id_pembagian)
    {
        $pembagian_karyawan = PembagianKaryawan::where('id_pembagian', $id_pembagian)->get();
        return response()->json([
            'success' => 1,
            'message' => 'Get data pembagian karyawan succeed',
            'data' => $pembagian_karyawan
        ]);
    }

    public function upload(Request $request)
    {
        $array = [];
        $id_pembagian = $request->id_pembagian;
        $employees = Excel::toArray(new PembagianKaryawanImport, $request->file('file'));
        $ignoredCount = 0;
        $savedCount = 0;

        foreach ($employees[0] as $key => $employee) {
            if ($key == 0) {
                foreach ($employee as $_key => $e) {
                    $column[$e] = $_key;
                }
            } else {
                $nik = $employee[$column['nik']];
                $cek = PembagianKaryawan::where('nik', $nik)
                    ->where('id_pembagian', $id_pembagian)->first();

                if ($cek == null) {
                    $nama = $employee[$column['nama']];
                    $department = $employee[$column['department']];
                    $lokasi = $employee[$column['lokasi']];
                    $keterangan = $employee[$column['keterangan']];

                    $pembagian_karyawan = new PembagianKaryawan;
                    $pembagian_karyawan->id_pembagian = $id_pembagian;
                    $pembagian_karyawan->nik = $nik;
                    $pembagian_karyawan->nama = $nama;
                    $pembagian_karyawan->department = $department;
                    $pembagian_karyawan->status_ambil = 'belum';
                    $pembagian_karyawan->keterangan = $keterangan;
                    $pembagian_karyawan->lokasi_pembagian = $lokasi;
                    $pembagian_karyawan->created_by = Auth::user()->name;
                    $pembagian_karyawan->save();
                    $savedCount++;
                } else {
                    $ignoredCount++;
                }
            }
        }

        return response()->json([
            'success' => 1,
            'message' => 'Upload data succeed',
            'ignored_count' => $ignoredCount,
            'saved_count' => $savedCount,
        ]);
    }
}
