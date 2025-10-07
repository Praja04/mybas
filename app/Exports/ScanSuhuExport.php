<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\Auth;
use App\CekSuhu;

class ScanSuhuExport implements FromArray
{
    protected $tanggal;

    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function array() : array
    {
        $all_data = [];
        $where_per_department = [];
        $where_per_department[] = ['dept', Auth::user()->department->name];
        $where_per_department[] = ['tanggal', $this->tanggal];   
        $data_per_department = CekSuhu::where($where_per_department)->get();
        $all_data[] = ['NO', 'TANGGAL', 'NIK', 'NAMA', 'SUHU', 'WAKTU SCAN'];
        foreach($data_per_department as $key => $data) {
            $all_data[] = [$key+1,$data->tanggal,$data->nik, $data->nama,$data->suhu,$data->waktu_scan];
        }
        return $all_data;
    }
}
