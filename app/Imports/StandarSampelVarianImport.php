<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\StandarSampelVarian;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class StandarSampelVarianImport implements Tomodel, WithHeadingRow
{
    public function model(array $row)
    {
         return new StandarSampelVarian ([
            'no_standar' => $row['no_standar'] . " ". date('Y-m-d'),
            'jenis_sampel' => $row['jenis_sampel'],
            'jenis_varian' => $row['varian'],
            'nik' => Auth::user()->username,
            'tgl_pengisian' => date('Y-m-d'),
            'tgl_edit' => date('Y-m-d'),
            'jam_pengisian' => date('H:i:s'),
        ]);
  }
}