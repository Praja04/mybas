<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\ProductionOrderModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class ProductionOrderImport implements Tomodel, WithHeadingRow
{
    public function model(array $row)
    {
         return new ProductionOrderModel ([
            'prod_order' => $row['prod_order'],
            'varian' => $row['varian'],
            'shift' => $row['shift'],
            'group' => $row['group'],
            'no_mesin' => $row['kode_mesin'],
            'tgl_pengisian' => date('Y-m-d'),
            'jam_pengisian' => date('H:i'),
            'nik_pembuat' => Auth::user()->username,
            'nama_pembuat' => Auth::user()->name,
            'jam_edit' => date('H:i'),
            'tgl_edit' => date('Y-m-d'),
            'status' => 1,
        ]);
  }
}