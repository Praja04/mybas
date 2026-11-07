<?php

namespace App\Exports\PoolQCC;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateParticipantExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'NIK *',
            'Nama Karyawan *',
            'Departemen',
            'Kode RFID Card (Opsional)',
        ];
    }

    public function array(): array
    {
        return [
            [
                '10023456',
                'Ahmad Fauzi',
                'Produksi',
                '0012345678',
            ],
            [
                '10023457',
                'Dewi Lestari',
                'Quality Control',
                '0023456789',
            ],
            [
                '10023458',
                'Rudi Hermawan',
                'Warehouse',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo header
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

