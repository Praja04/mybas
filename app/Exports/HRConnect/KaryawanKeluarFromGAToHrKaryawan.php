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

class KaryawanKeluarFromGAToHrKaryawan implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $karyawanCollection = new Collection();

        foreach ($this->data as $item) {
            $karyawan = HrKaryawan::where('id', $item['id_karyawan'])->first();

            if ($karyawan) {
                $tglKeluar = (! empty($karyawan->tanggal_keluar) && $karyawan->tanggal_keluar !== '0000-00-00') ? \Carbon\Carbon::parse($karyawan->tanggal_keluar)->format('d-m-Y') : now()->format('d-m-Y');

                $karyawanCollection->push([
                    'nik'            => $karyawan->nik,
                    'nama'           => $karyawan->nama,
                    'kode_divisi'    => $karyawan->kode_divisi,
                    'kode_bagian'    => $karyawan->kode_bagian,
                    'kode_admin'     => $karyawan->kode_admin,
                    'alasan_keluar'  => $item['alasan'] ?? 'Pencabutan Loker Massal',
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
            'Kode Divisi',
            'Kode Bagian',
            'Kode Admin',
            'Alasan Keluar (GA)',
            'Tanggal Keluar',
        ];
    }

    public function map($row): array
    {
        return [
            ' ' . $row['nik'],
            $row['nama'],
            $row['kode_divisi'],
            $row['kode_bagian'],
            $row['kode_admin'],
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
                    'startColor' => ['rgb' => '4F81BD'], // Warna Biru Soft disamakan persis!
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
