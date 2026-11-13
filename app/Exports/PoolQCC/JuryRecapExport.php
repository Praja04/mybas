<?php

namespace App\Exports\PoolQCC;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JuryRecapExport implements WithMultipleSheets
{
    use Exportable;

    protected $recapData;

    public function __construct(array $recapData)
    {
        $this->recapData = $recapData;
    }

    public function sheets(): array
    {
        return [
            new JuryRecapSummarySheet($this->recapData),
            new JuryRecapDetailSheet($this->recapData),
        ];
    }
}

class JuryRecapSummarySheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $recapData;

    public function __construct(array $recapData)
    {
        $this->recapData = $recapData;
    }

    public function title(): string
    {
        return 'Klasemen Dewan Juri';
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'Predikat Penghargaan',
            'Kode Booth',
            'Nama Booth / Inovasi',
            'Kategori',
            'Lokasi Stand',
            'Rata-Rata Skor',
            'Skor Tertinggi',
            'Skor Terendah',
            'Jumlah Juri Menilai',
            'Total Juri Aktif',
            'Kelengkapan (%)',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $booths = $this->recapData['booths'] ?? [];
        $totalActiveJudges = $this->recapData['total_active_judges'] ?? 0;

        foreach ($booths as $index => $booth) {
            $rank = $index + 1;
            $avg = (float)($booth['avg_score'] ?? 0);

            $award = 'Finalis';
            if ($rank === 1 && $avg > 0) {
                $award = 'Juara 1 (The Best Booth)';
            } elseif ($rank === 2 && $avg > 0) {
                $award = 'Juara 2 (Runner Up)';
            }

            $rows[] = [
                $rank,
                $award,
                $booth['code'] ?? '-',
                $booth['name'] ?? '-',
                $booth['category'] ?? '-',
                $booth['location'] ?? '-',
                number_format($avg, 2),
                number_format((float)($booth['max_score'] ?? 0), 2),
                number_format((float)($booth['min_score'] ?? 0), 2),
                (int)($booth['judges_evaluated_count'] ?? 0),
                $totalActiveJudges,
                ($booth['completion_pct'] ?? 0) . '%',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = max(1, $sheet->getHighestRow());
        $highestCol = $sheet->getHighestColumn();

        // Style Header
        $sheet->getStyle('A1:' . $highestCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Border & Alignment untuk seluruh sel data
        if ($highestRow > 1) {
            $sheet->getStyle('A1:' . $highestCol . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            // Alignment kolom
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}

class JuryRecapDetailSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $recapData;

    public function __construct(array $recapData)
    {
        $this->recapData = $recapData;
    }

    public function title(): string
    {
        return 'Detail Penilaian Juri';
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Penilaian',
            'Kode Booth',
            'Nama Booth',
            'Nama Dewan Juri',
            'Departemen Juri',
            'Total Skor (1-100)',
            'Catatan / Masukan Dewan Juri',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;
        $booths = $this->recapData['booths'] ?? [];

        foreach ($booths as $booth) {
            $evaluations = $booth['evaluations'] ?? [];
            foreach ($evaluations as $eval) {
                $rows[] = [
                    $no++,
                    $eval['evaluated_at'] ?? '-',
                    $booth['code'] ?? '-',
                    $booth['name'] ?? '-',
                    $eval['judge_name'] ?? '-',
                    $eval['judge_dept'] ?? '-',
                    number_format((float)($eval['score'] ?? 0), 2),
                    $eval['notes'] ?? '-',
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = max(1, $sheet->getHighestRow());
        $highestCol = $sheet->getHighestColumn();

        // Style Header
        $sheet->getStyle('A1:' . $highestCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7C3AED'], // Violet-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($highestRow > 1) {
            $sheet->getStyle('A1:' . $highestCol . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}

