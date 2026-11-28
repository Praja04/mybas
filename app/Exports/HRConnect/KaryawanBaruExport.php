<?php
namespace App\Exports\HRConnect;

use App\HrKaryawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanBaruExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $ids = collect($this->data)->pluck("idCheckwish")->filter()->toArray();

        if (empty($ids)) {
            return new Collection();
        }

        return HrKaryawan::whereIn('id', $ids)->get();
    }

    public function headings(): array
    {
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

    public function map($karyawan): array
    {
        $proses = '-';
        if ($karyawan->p_in == 'Y') {
            $proses = 'IN';
        } elseif ($karyawan->p_no == 'Y') {
            $proses = 'NO-IN';
        }

        return [
            $karyawan->nama,
            ' ' . $karyawan->nik,
            $karyawan->kode_divisi,
            $karyawan->kode_bagian,
            $proses,
            $karyawan->kode_admin,
            $karyawan->kode_group,
            $karyawan->tanggal_masuk && $karyawan->tanggal_masuk !== '0000-00-00'
                ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d-m-Y')
                : '-',
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
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
