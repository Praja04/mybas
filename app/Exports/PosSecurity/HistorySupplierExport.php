<?php

namespace App\Exports\PosSecurity;

use App\Models\PosSecurity\GaVisitorTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistorySupplierExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    private $rowNumber = 0;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        $query = GaVisitorTransaction::query()
            ->whereIn('keterangan', ['SUPIR', 'KERNET']);

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween('datein', [$this->startDate, $this->endDate]);
        } elseif (!empty($this->startDate)) {
            $query->where('datein', $this->startDate);
        }

        return $query->orderBy('datein', 'desc')
            ->orderBy('timein', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Supir/Kernet',
            'Keterangan',
            'Perusahaan',
            'No. Polisi',
            'No. Kartu',
            'No. HP Driver',
            'Tanggal Masuk',
            'Jam Masuk',
            'Tanggal Keluar',
            'Jam Keluar',
            'Status Kartu',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->namavisitor ?: '-',
            $row->keterangan ?: '-',
            $row->namacomp ?: '-',
            $row->nopol ?: '-',
            $row->no_kartu ?: '-',
            $row->nohpdriver ?: '-',
            $row->datein ? Carbon::parse($row->datein)->format('d-m-Y') : '-',
            $row->timein ? Carbon::parse($row->timein)->format('H:i') : '-',
            $row->dateout ? Carbon::parse($row->dateout)->format('d-m-Y') : '-',
            $row->timeout ? Carbon::parse($row->timeout)->format('H:i') : '-',
            $row->kartu_dikembalikan ? 'Dikembalikan' : 'Belum Dikembalikan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '198754'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
