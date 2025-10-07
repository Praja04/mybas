<?php

namespace App\Imports;

use App\DataTamu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Maatwebsite\Excel\Concerns\WithCustomValueBinder;


class DataTamuImport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements WithCustomValueBinder, ToModel, WithStartRow
{
    public function model(array $row)
    {
        return new DataTamu([
            'nama' => $row[0],
            'nama_instansi' => $row[1],
            'jenis_kunjungan' => $row[2],
            'no_identitas' => $row[3],
            'tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])->createFromFormat('m/d/Y')->format('Y-m-d'),
            'bertemu_dengan' => $row[5],
            'jawaban_pertanyaan_1' => $row[6],
            'jawaban_pertanyaan_2' => $row[7],
            'jawaban_pertanyaan_3' => $row[8],
            'keterangan_pertanyaan_3' => $row[9],
            'jawaban_pertanyaan_4' => $row[10],
            'jawaban_pertanyaan_5' => $row[11],
            'jawaban_pertanyaan_6' => $row[12],
            'jawaban_pertanyaan_7' => $row[13]
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
