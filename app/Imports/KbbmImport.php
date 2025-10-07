<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Kbbm;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KbbmImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Kbbm([
            'department' => $row['dept'],
            'nik' => $row['nik'],
            'nama_lengkap' => $row['nama'],
            'tanggal_masuk' => $row['tgl_masuk'] == '' ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tgl_masuk'])
        ]);
    }
}
