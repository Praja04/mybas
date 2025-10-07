<?php

namespace App\Imports\Ecafesedaap;

use Maatwebsite\Excel\Concerns\ToArray;

class OvertimeImport implements ToArray
{
    /**
    * @param array $array
    */
    public function array(array $row)
    {
        return [
            'nik' => $row[0],
            'nama' => $row[1],
        ];
    }
}
