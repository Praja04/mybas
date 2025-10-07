<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\MasterMesin;
use App\MasterVarian;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class MasterMesinImport implements Tomodel, WithHeadingRow
{
    public function model(array $row)
    {
         return new MasterMesin([
            'line' => $row['line'],
            'no_mesin' => $row['no_mesin'],
            'group' => $row['group'],
            'jenis_mesin' => $row['jenis_mesin'],
            'NoSeq' => $row['no_seq'],
            'workcenter' => $row['workcenter'],
        ]);
  }
}