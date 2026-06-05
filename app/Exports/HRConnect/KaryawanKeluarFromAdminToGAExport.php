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

class KaryawanKeluarFromAdminToGAExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $list_karyawan;

    public function __construct($list_karyawan)
    {
        $this->list_karyawan = $list_karyawan;
    }

    public function collection()
    {
        $all_niks = collect($this->list_karyawan)->pluck('nik')->filter()->toArray();

        if (empty($all_niks)) {
            return new Collection();
        }

        $karyawans = HrKaryawan::whereIn('nik', $all_niks)->get()->keyBy('nik');

        $karyawanCollection = new Collection();

        foreach ($this->list_karyawan as $item) {
            $nik = $item['nik'];

            $karyawan = $karyawans->get($nik);

            if ($karyawan) {
                $tglKeluar = (! empty($item['tanggal_keluar']))
                    ? \Carbon\Carbon::parse($item['tanggal_keluar'])->format('d-m-Y')
                    : now()->format('d-m-Y');

                $karyawanCollection->push([
                    'nik'            => $karyawan->nik,
                    'nama'           => $karyawan->nama,
                    'kode_bagian'    => $karyawan->kode_bagian,
                    'alasan_keluar'  => $item['alasan_keluar'] ?? 'Checkout Admin',
                    'tanggal_keluar' => $tglKeluar,
                ]);
            }
        }

        return $karyawanCollection;
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Lengkap',
            'Kode Bagian',
            'Alasan Keluar',
            'Tanggal Keluar',
        ];
    }

    public function map($row): array
    {
        return [
            ' ' . $row['nik'],
            $row['nama'],
            $row['kode_bagian'],
            $row['alasan_keluar'],
            $row['tanggal_keluar'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5A93A'],
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
