<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\ParameterPengecekan;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class ParameterPengecekanImport  implements Tomodel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new ParameterPengecekan ([
            'no_standar' => $row['no_standar'],
            'parameter' => $row['parameter'],
            'nilai' => $row['nilai'],
            'satuan_parameter' => $row['satuan_parameter'],
            'tgl_pengisian' => date('Y-m-d'),
            'tgl_edit' => date('Y-m-d'),
            'jam_pengisian' => date('H:i:s'),
        ]);
    }
}
