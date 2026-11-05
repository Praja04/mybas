<?php

namespace App\Exports\PoolQCC;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateQuestionExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'Kode Booth *',
            'Teks Pertanyaan *',
            'Pilihan A *',
            'Pilihan B *',
            'Pilihan C *',
            'Pilihan D *',
            'Pilihan E (Opsional)',
            'Kunci Jawaban (A/B/C/D/E) *',
            'Bobot Poin (Default 1)',
            'Pembahasan / Penjelasan (Opsional)',
        ];
    }

    public function array(): array
    {
        return [
            [
                'B01',
                'Apa kepanjangan dan prinsip utama dari siklus PDCA dalam aktivitas QCC?',
                'Plan, Do, Check, Act untuk perbaikan berkelanjutan',
                'Process, Delivery, Control, Action',
                'Program, Develop, Count, Assess',
                'Produce, Distribute, Check, Analyze',
                '',
                'A',
                1,
                'Siklus PDCA (Plan, Do, Check, Act) adalah metodologi inti kaizen untuk continuous improvement.',
            ],
            [
                'B01',
                'Manakah dari pilihan berikut yang termasuk dalam 7 Tools of Quality Control?',
                'Diagram Pareto',
                'SWOT Analysis',
                'SMART Goal Setting',
                'Business Model Canvas',
                '',
                'A',
                1,
                'Diagram Pareto merupakan salah satu dari 7 Tools QC untuk memprioritaskan masalah terbesar.',
            ],
            [
                'B02',
                'Metode pencegahan kesalahan manusia secara otomatis dalam industri manufaktur dikenal dengan istilah?',
                'Poka-Yoke (Mistake Proofing)',
                'Just In Time',
                'Kanban System',
                'Overall Equipment Effectiveness',
                '',
                'A',
                1,
                'Poka-Yoke adalah mekanisme otomatis untuk mencegah terjadinya kesalahan operasi atau cacat produk.',
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
                    'startColor' => ['rgb' => 'D97706'], // Amber header
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

