<?php
namespace App\Exports\HRConnect;

use App\HrKaryawan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanFinalOnboardExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $tgl_masuk;

    public function __construct($tgl_masuk)
    {
        $this->tgl_masuk = $tgl_masuk;
    }

    public function query()
    {
        // PIPELINE TERAKHIR: Narik orang yang UDAH LOLOS SEMUANYA
        return HrKaryawan::query()
            ->where('tanggal_masuk', $this->tgl_masuk)
            ->where('in_kode_group', 'Y')
            ->where('in_complete', 'Y')
            ->where('is_goobag', 'Y')
            ->where('active', 'Y')
            ->orderBy('nama', 'asc');
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Lengkap',
            'Departemen',
            'Bagian',
            'Status Onboarding',
        ];
    }

    public function map($karyawan): array
    {
        return [
            ' ' . $karyawan->nik, // Biar 0 di depan gak hilang
            $karyawan->nama,
            $karyawan->kode_divisi,
            $karyawan->kode_bagian,
            'Selesai (Loker & Fasilitas)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3699FF'], // Warna Biru senada dengan email lu
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
