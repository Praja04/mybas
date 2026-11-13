<?php

namespace App\Exports\PoolQCC;

use App\Models\PoolQCC\PoolBooth;
use App\Models\PoolQCC\PoolParticipant;
use App\Models\PoolQCC\PoolVote;
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

class ParticipantVotesExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new ParticipantSummarySheet(),
            new ParticipantVotesDetailSheet(),
        ];
    }
}

class ParticipantSummarySheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Rekap Peserta';
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Karyawan / Peserta',
            'Departemen',
            'No. Kartu RFID',
            'Total Skor (Poin)',
            'Booth Dikunjungi',
            'Total Stand Booth',
            'Status Kunjungan',
            'Aktivitas Terakhir',
        ];
    }

    public function array(): array
    {
        $totalBooths = PoolBooth::where('is_active', true)->count();
        if ($totalBooths <= 0) {
            $totalBooths = PoolBooth::count();
        }
        $maxBooths = max(1, $totalBooths);

        $participants = PoolParticipant::orderBy('total_score', 'desc')
            ->orderBy('booths_voted_count', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $rows = [];
        $no = 1;

        foreach ($participants as $p) {
            $voted = (int)$p->booths_voted_count;
            $status = ($voted >= $maxBooths) ? 'Lengkap (Semua Booth)' : 'Belum Lengkap';

            $rows[] = [
                $no++,
                "'" . ($p->nik ?? '-'), // Tanda kutip agar NIK angka panjang tidak terpotong / scientific notation di Excel
                $p->name ?? '-',
                $p->department ?? '-',
                "'" . ($p->rfid ?? '-'),
                (int)$p->total_score,
                $voted,
                $maxBooths,
                $status,
                $p->last_activity_at ? $p->last_activity_at->format('d/m/Y H:i:s') : '-',
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
                'startColor' => ['rgb' => '059669'], // Emerald-600
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

            $sheet->getStyle('A2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}

class ParticipantVotesDetailSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Riwayat Voting Booth';
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Tapping / Vote',
            'NIK Peserta',
            'Nama Peserta',
            'Departemen',
            'Kode Booth',
            'Nama Stand Booth',
            'Pertanyaan Soal',
            'Jawaban Peserta',
            'Kunci Jawaban',
            'Status Jawaban',
            'Poin Diperoleh',
        ];
    }

    public function array(): array
    {
        $votes = PoolVote::with(['participant', 'booth', 'question'])
            ->latest('id')
            ->get();

        $rows = [];
        $no = 1;

        foreach ($votes as $v) {
            $participant = $v->participant;
            $booth = $v->booth;
            $question = $v->question;

            $statusText = $v->is_correct ? 'BENAR' : 'SALAH';

            $rows[] = [
                $no++,
                $v->answered_at ? $v->answered_at->format('d/m/Y H:i:s') : ($v->created_at ? $v->created_at->format('d/m/Y H:i:s') : '-'),
                "'" . ($participant ? $participant->nik : '-'),
                $participant ? $participant->name : '-',
                $participant ? $participant->department : '-',
                $booth ? $booth->code : '-',
                $booth ? $booth->name : '-',
                $question ? $question->question_text : '-',
                $v->participant_answer ?? '-',
                $question ? $question->correct_answer : '-',
                $statusText,
                (int)$v->points_awarded,
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
                'startColor' => ['rgb' => '0284C7'], // Sky-600
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

            $sheet->getStyle('A2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
