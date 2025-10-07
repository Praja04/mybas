<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\MasterMesin;
use App\MasterRepair;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class MasterRepairImport implements Tomodel, WithHeadingRow
{
    public function model(array $row)
    {
         return new MasterRepair([
            'jenis_mesin' => $row['jenis_mesin'],
            'no_mesin' => $row['no_mesin'],
            'reason' => $row['reason'],
            'repair' => $row['repair'],
            'kategori' => $row['kategori']
        ]);
  }
}