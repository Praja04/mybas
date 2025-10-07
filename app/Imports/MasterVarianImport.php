<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\MasterVarian;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class MasterVarianImport implements Tomodel, WithHeadingRow
{
    public function model(array $row)
    {
         return new MasterVarian([
            'rasa' => $row['varian_rasa'],
        ]);
  }
}