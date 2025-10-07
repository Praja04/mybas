<?php

namespace App\Imports;

use App\ECafeSedaapModel;
use Maatwebsite\Excel\Concerns\ToModel;


class ExcelImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $tgl = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('Y-m-d');
        $tanggal = explode('-', $tgl);
        $shift = $row[1];
        $id_pesanan = 'TR-' . $tanggal[2] . $tanggal[1] . $tanggal[0] . '-' . $shift;
        return new ECafeSedaapModel([
            'id_pesanan' => $id_pesanan,
            'tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]),
            'shift' => $row[1],
            'jumlah' => $row[2]
        ]);
    }
}
