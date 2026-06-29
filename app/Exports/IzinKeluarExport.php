<?php
namespace App\Exports;

use App\LunchBreak;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IzinKeluarExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = LunchBreak::query();

        if ($this->filters['tab'] == 'today') {
            $query->whereDate('jam_keluar', today());
        } elseif ($this->filters['tab'] == 'all') {
            $query->whereNotNull('jam_keluar');
        }

        if ($this->filters['divisi']) {
            $query->where('divisi', $this->filters['divisi']);
        }

        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['tanggal']) {
            $dates = explode(' - ', $this->filters['tanggal']);
            if (count($dates) == 2) {
                $query->whereDate('jam_keluar', '>=', $dates[0])
                      ->whereDate('jam_keluar', '<=', $dates[1]);
            } else {
                $query->where('jam_keluar', 'like', $this->filters['tanggal'] . '%');
            }
        }

        $query->orderBy('jam_keluar', 'asc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIK',
            'Nama',
            'Divisi',
            'Jam Keluar',
            'Jam Masuk',
            'Menit Terlambat',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row->jam_keluar)->format('d-m-Y'),
            $row->nik,
            ucfirst($row->nama),
            ucfirst($row->divisi),
            Carbon::parse($row->jam_keluar)->format('H:i'),
            $row->jam_masuk != null ? Carbon::parse($row->jam_masuk)->format('H:i') : '-',
            $row->menit_terlambat === null ? '-' : ($row->menit_terlambat == 0 ? '-' : $row->menit_terlambat . ' Menit'),
            ucfirst($row->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
        ];
    }
}
