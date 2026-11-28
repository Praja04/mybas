<?php

namespace App\Exports\PoolQCC;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateBoothExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'Kode Booth (Opsional)',
            'Nama Booth / Judul Inovasi *',
            'Departemen *',
            'Nama PIC',
            'Kontak PIC',
            'Lokasi',
            'Deskripsi Singkat',
        ];
    }

    public function array(): array
    {
        return [
            [
                'B01',
                'Circle Kaizen Line Produksi',
                'Produksi',
                'Budi Santoso',
                '081234567890',
                'Hall A - Stand 01',
                'Inovasi efisiensi cycle time lini assembling dengan modifikasi feeder otomatis.',
            ],
            [
                'B02',
                'Zero Defect Quality Circle',
                'Quality Control',
                'Siti Aminah',
                '081298765432',
                'Hall A - Stand 02',
                'Implementasi poka-yoke sensor untuk mencegah cacat label kemasan produk.',
            ],
            [
                'B03',
                'Smart Warehouse Logistics',
                'Warehouse',
                'Hendra Gunawan',
                '085612345678',
                'Hall A - Stand 03',
                'Optimalisasi rute picking barang menggunakan sistem barcode zonasi.',
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

