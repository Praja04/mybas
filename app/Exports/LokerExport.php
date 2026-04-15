<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LokerExport implements FromQuery, WithHeadings, WithMapping
{
    protected $gender;

    public function __construct($gender)
    {
        $this->gender = $gender;
    }

    public function query()
    {
        $prefix = ($this->gender == 'L') ? 'LP' : 'LW';

        return DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->where('lp.is_active', 'Y');
            })
            ->select(
                'lr.no_loker',
                'lr.is_active',
                'lp.nik',
                'lp.nama',
                'lp.kategori_karyawan',
                'lp.divisi'
            )
            ->where('lr.gender', $this->gender)
            ->orderBy('lr.no_loker', 'asc');
    }

    public function map($row): array
    {
        return [
            $row->no_loker,
            $row->status_rak == 'Y' ? 'Aktif' : 'Rusak/Perbaikan',
            $row->nik ?? '-',
            $row->nama ?? '-',
            ucwords(str_replace('_', ' ', $row->kategori_karyawan ?? '-')),
            $row->divisi ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No. Loker',
            'Status Loker',
            'NIK Karyawan',
            'Nama Karyawan',
            'Jenis Karyawan',
            'Departemen',
        ];
    }
}
