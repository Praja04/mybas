<?php

namespace App\Http\Controllers;

use App\DataTamu;
use App\Imports\DataTamuImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataTamuController extends Controller
{
    public function manage()
    {
        $data_tamu = DataTamu::orderBy('tanggal', 'desc')->get();
        return view('data-tamu.manage', ['data_tamu' => $data_tamu]);
    }

    public function upload(Request $request)
    {
        $excel = $request->file('file');
        $array = Excel::toArray(new DataTamuImport, $excel);

        // dd($array);
        foreach ($array[0] as $data) {
            $cek = DataTamu::where('no_identitas', $data[4])->where('tanggal', $data[5])->first();
            if($cek == null) {
                $tanggal = date_format(date_create($data[5]), 'Y-m-d');
                $data_tamu = new DataTamu;
                $data_tamu->nama = $data[1];
                $data_tamu->nama_instansi = $data[2];
                $data_tamu->jenis_kunjungan = $data[3];
                $data_tamu->no_identitas = $data[4];
                $data_tamu->tanggal = $tanggal;
                $data_tamu->bertemu_dengan = $data[6];
                $data_tamu->jawaban_pertanyaan_1 = $data[7];
                $data_tamu->jawaban_pertanyaan_2 = $data[8];
                $data_tamu->jawaban_pertanyaan_3 = $data[9];
                $data_tamu->keterangan_pertanyaan_3 = $data[10];
                $data_tamu->jawaban_pertanyaan_4 = $data[11];
                $data_tamu->jawaban_pertanyaan_5 = $data[12];
                $data_tamu->jawaban_pertanyaan_6 = $data[13];
                $data_tamu->jawaban_pertanyaan_7 = $data[14];
                $data_tamu->save();
            }
        }
        return redirect('/hr/data-tamu')->with('success', 'Upload Succeed');
    }
}
