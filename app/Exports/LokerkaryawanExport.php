<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LokerkaryawanExport implements FromCollection, WithHeadings
{
    protected $kode_area;

    function __construct($kode_area)
    {
        $this->kode_area = substr($kode_area, -3, 2);
    }
    public function collection()
    {
        $data = DB::table('loker_master_user')
            ->select(
                'kode_area',
                'kode_blok',
                'no_loker',
                'nama',
                'nik',
                'jk',
                'divisi',
                'bagian',
                'kode_kontrak',
                DB::raw('(CASE 
                        WHEN nama != null or nama != "" THEN "Terisi"
                        ELSE "Free / Rusak" 
                        END) AS status_loker')
            )
            ->where('kode_area', 'LIKE', '%' . $this->kode_area . '%')
            ->get();

        return collect($data);
    }
    public function headings(): array
    {
        return [
            'Kode Area',
            'Kode Blok',
            'No. Loker',
            'Nama',
            'NIK',
            'Jenis Kelamin',
            'Divisi',
            'Bagian',
            'Kode Kontrak',
            'Status',
        ];
    }
}
