<?php

namespace App\Exports\PosSecurity;

use App\Models\PosSecurity\GaVisitorVendorTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryVendorExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithCustomValueBinder
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
        $query = GaVisitorVendorTransaction::query();

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end   = Carbon::parse($this->endDate)->endOfDay();
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('datein', [Carbon::parse($this->startDate)->toDateString(), Carbon::parse($this->endDate)->toDateString()])
                  ->orWhereBetween('createdon', [$start, $end]);
            });
        } elseif (!empty($this->startDate)) {
            $date = Carbon::parse($this->startDate)->toDateString();
            $query->where(function ($q) use ($date) {
                $q->where('datein', $date)
                  ->orWhereDate('createdon', $date);
            });
        }

        return $query->orderBy('createdon', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Perusahaan',
            'Nama Pengunjung',
            'Tanggal Lahir',
            'Bertemu Dgn',
            'Keperluan',
            'Departemen',
            'No. Kartu',
            'No. Identitas',
            'Jenis Kartu',
            'Nomor Polisi',
            'Pakai Kacamata',
            'Kondisi Kacamata (Masuk)',
            'Kondisi Kacamata (Keluar)',
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
            $row->namacomp ?: '-',
            $row->namavisitor ?: '-',
            $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-',
            $row->host ?: '-',
            $row->keperluan ?: '-',
            $row->hostdeptid ?: '-',
            $row->no_kartu ?: '-',
            $row->no_ktp_sim ?: '-',
            $row->type ?: '-',
            $row->nopol ?: '-',
            $row->is_kacamata ? 'Ya' : 'Tidak',
            $row->is_kacamata ? ($row->kondisi_kacamata ?: '-') : '-',
            $row->is_kacamata ? ($row->kondisi_kacamata_out ?: '-') : '-',
            $row->datein ? Carbon::parse($row->datein)->format('d-m-Y') : ($row->createdon ? Carbon::parse($row->createdon)->format('d-m-Y') : '-'),
            $row->timein ? Carbon::parse($row->timein)->format('H:i') : ($row->createdon ? Carbon::parse($row->createdon)->format('H:i') : '-'),
            $row->dateout ? Carbon::parse($row->dateout)->format('d-m-Y') : '-',
            $row->timeout ? Carbon::parse($row->timeout)->format('H:i') : '-',
            $row->kartu_dikembalikan ? 'Dikembalikan' : 'Belum Dikembalikan',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Cegah pembulatan otomatis Excel untuk angka panjang (seperti NIK 16 digit, No Kartu, dsb)
        if (is_numeric($value) && (strlen((string)$value) >= 8 || preg_match('/^0\d+/', (string)$value))) {
            $cell->setValueExplicit((string)$value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_TEXT, // No. Kartu
            'I' => NumberFormat::FORMAT_TEXT, // No. Identitas (NIK/KTP/SIM)
            'K' => NumberFormat::FORMAT_TEXT, // Nomor Polisi
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
