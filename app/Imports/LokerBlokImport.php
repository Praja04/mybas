<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;

class LokerBlokImport implements ToCollection, WithHeadingRow
{
    

    public function collection(Collection $rows)
    {
        $data = [];
        
        foreach($rows as $row)
        {
            $data[] =  array(
            'kode_blok' => 'BLOK'. ' '.$row['kode_blok'],
            'no_loker' => $row['kode_blok'] . '-'. $row['no_loker'],
            'kode_area' => $row['kode_area'],
            );
        }

        $blok = [];

        foreach($rows as $key => $value)
        {
            $blok = array(
                'kode_blok' => 'BLOK'. ' '. $value['kode_blok'],
                'kode_area' => $value['kode_area'],
            );
        }

        DB::table('loker_master_nomer')->insert($data);
        DB::table('loker_master_blok')->insert($blok);

    }
}
