<?php
namespace App\Exports\HRConnect\Admin;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateCheckoutKaryawanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        // Urutan 100% sama persis dengan tabel di Modal Info
        return [
            'Nama',
            'NIK',
            'Divisi',
            'Kode Bagian',
            'Kode Admin',
            'Kode Group',
            'Alasan Keluar',
            'Tgl Keluar',
        ];
    }

    public function array(): array
    {
        // Dummy data sesuai Modal Info
        return [
            [
                'Testing 1',
                '123456789',
                'PRO',
                'PRN_02',
                'PAS_PRN_A',
                'ENG_PRN_A',
                'Habis Kontrak',
                '10/20/2024',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    // Warna MERAH (Danger) biar Admin "ngeh" ini form hapus/checkout
                    'startColor' => ['rgb' => 'DC3545'],
                ],
            ],
        ];
    }
}
