<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\MasterSamplingGramatur;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class MasterSamplingGramaturImport implements Tomodel, WithHeadingRow
{
    public function model(array $row)
    {
         return new MasterSamplingGramatur([
            'shift' => $row['shift'],
            'jam_ke' => $row['jam_ke'],
            'waktu_mulai' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['waktu_mulai']) ,
            'waktu_selesai' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['waktu_selesai'])
        ]);
  }
}