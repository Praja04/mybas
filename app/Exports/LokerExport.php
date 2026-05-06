<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LokerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $gender;

    public function __construct($gender)
    {
        $this->gender = $gender;
    }

    public function collection()
    {
        $data = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->where('lp.is_active', 'Y');
            })
            ->select(
                'lr.no_loker',
                'lr.is_active as status_rak',
                'lr.keterangan_kondisi as kondisi',
                'lr.kondisi_fisik',
                'lr.catatan_admin',
                'lp.nik',
                'lp.nama',
                'lp.divisi',
                'lp.kategori_karyawan'
            )
            ->where('lr.gender', $this->gender)
            ->orderBy('lr.no_loker', 'asc')
            ->get();

        return $data->groupBy('no_loker')->map(function ($items) {
            $first = $items->first();
            return (object) [
                'no_loker'      => $first->no_loker,
                'status_rak'    => $first->status_rak,
                'kondisi'       => $first->kondisi,
                'kondisi_fisik' => $first->kondisi_fisik,
                'catatan_admin' => $first->catatan_admin,
                'nik'           => $items->pluck('nik')->filter()->implode("\n"),
                'nama'          => $items->pluck('nama')->filter()->implode("\n"),
                'divisi'        => $items->pluck('divisi')->filter()->implode("\n"),
                'kategori'      => $items->pluck('kategori_karyawan')->map(function ($kat) {
                    return ucwords(str_replace('_', ' ', $kat ?? '-'));
                })->filter()->implode("\n"),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. Loker',
            'Loker Sepatu',
            'Status Loker Sepatu',
            'Loker Baju',
            'Status Loker Baju',
            'NIK Karyawan',
            'Nama Karyawan',
            'Kategori Karyawan',
            'Dept / Divisi',
            'Keterangan',
            'Catatan',
        ];
    }

    public function map($row): array
    {
        $prefixS = ($this->gender == 'L') ? 'PS-' : 'WS-';
        $prefixB = ($this->gender == 'L') ? 'PB-' : 'WB-';
        // $status  = ($row->status_rak == 'Y') ? 'Aktif' : 'Rusak';

        $kondisiRaw = $row->kondisi_fisik ?: '';

        $statusSepatu = 'Aktif';
        $statusBaju   = 'Aktif';

        if (! empty($kondisiRaw) && strpos($kondisiRaw, '|') !== false) {
            $parts = explode('|', $kondisiRaw);

            foreach ($parts as $part) {
                $part = trim($part);
                if (stripos($part, 'Sepatu:') === 0) {
                    $statusSepatu = trim(substr($part, strlen('Sepatu:')));
                } elseif (stripos($part, 'Baju:') === 0) {
                    $statusBaju = trim(substr($part, strlen('Baju:')));
                }
            }
        }

        return [
            $row->no_loker,
            $prefixS . $row->no_loker,
            $statusSepatu,
            $prefixB . $row->no_loker,
            $statusBaju,
            $row->nik ?: '-',
            $row->nama ?: '-',
            $row->kategori ?: '-',
            $row->divisi ?: '-',
            $row->kondisi ?: '-',
            $row->catatan_admin ?: '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow   = $event->sheet->getHighestRow();
                $cellRange = 'A1:K' . $lastRow;

                // 1. ALIGNMENT & WRAP TEXT
                $event->sheet->getStyle($cellRange)->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true, // Kunci agar \n bekerja
                ]);

                // 2. AUTO ROW HEIGHT (PENTING!)
                // Agar baris yang isinya 2 nama otomatis jadi tinggi
                for ($i = 1; $i <= $lastRow; $i++) {
                    $event->sheet->getRowDimension($i)->setRowHeight(-1);
                }

                // 3. HEADER STYLING
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'],
                    ],
                ]);

                // 4. BORDERS
                $event->sheet->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray([
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ]);

                // Tips: Set kolom Nama (G) dan NIK (F) agar agak lebar dikit
                $event->sheet->getColumnDimension('G')->setWidth(30);
                $event->sheet->getColumnDimension('F')->setWidth(15);
            },
        ];
    }
}
