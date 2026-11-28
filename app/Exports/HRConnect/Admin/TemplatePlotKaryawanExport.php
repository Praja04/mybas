<?php
namespace App\Exports\HRConnect\Admin;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePlotKaryawanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        // Urutan disesuaikan dengan request lu: Proses duluan baru Admin & Group
        return [
            'Nama',
            'NIK',
            'Dept',
            'Kode Bagian',
            'Proses',
            'Kode Admin',
            'Kode Group',
            'Tgl Masuk',
        ];
    }

    public function array(): array
    {
        // Kita kasih dummy data biar Admin gampang nirunya
        return [
            [
                'Testing 1',
                '123456789',
                'PRO',
                'PRN_02',
                'IN',
                'PAS_PRN_A',
                'ENG_PRN_A',
                '10/9/2024',
            ],
            [
                'Testing 2',
                '132674758',
                'PRO',
                'PRN_02',
                'NO-IN',
                '-',
                '-',
                '10/9/2024',
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
                    'startColor' => ['rgb' => '10B981'], // Warna Hijau (Sukses/Template)
                ],
            ],
        ];
    }
}
