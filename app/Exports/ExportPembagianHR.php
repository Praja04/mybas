<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportPembagianHR implements FromCollection, WithHeadings
{
    use Exportable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        // Gunakan Query Builder untuk mengambil data dari kedua tabel
        $data = DB::table('hr_pembagian_karyawan')
            ->join('hr_pembagian', 'hr_pembagian_karyawan.id_pembagian', '=', 'hr_pembagian.id')
            ->where('hr_pembagian_karyawan.id_pembagian', $this->id)
            ->select(
                'hr_pembagian_karyawan.nik',
                'hr_pembagian_karyawan.nama',
                'hr_pembagian_karyawan.department',
                'hr_pembagian_karyawan.status_ambil',
                'hr_pembagian_karyawan.waktu_ambil',
                'hr_pembagian_karyawan.pic',
                'hr_pembagian_karyawan.keterangan',
                'hr_pembagian_karyawan.lokasi_pembagian',
                'hr_pembagian_karyawan.checkout_time'
            )
            ->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Nik',
            'Nama',
            'Department',
            'Status Ambil',
            'Waktu Ambil',
            'PIC',
            'Keterangan',
            'Lokasi Pembagian',
            'Checkout Time',
        ];
    }
}
