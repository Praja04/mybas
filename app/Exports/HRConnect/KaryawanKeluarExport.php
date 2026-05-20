<?php
namespace App\Exports\HRConnect;

use App\HrKaryawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanKeluarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $tanggal;
    protected $showAll;

    public function __construct($tanggal, $showAll)
    {
        $this->tanggal = $tanggal;
        $this->showAll = $showAll;
    }

    public function collection()
    {
        // $query = HrKaryawan::where('tanggal_masuk', '>', '2025-01-01')
        //     ->orderBy('tanggal_masuk', 'asc');
        $query = HrKaryawan::with(['penghuni' => function($q) {
            $q->where('is_active', 'Y');
        }])->where('tanggal_masuk', '>', '2025-01-01')
        ->orderBy('tanggal_masuk','desc');

        if ($this->showAll == 0 && ! empty($this->tanggal)) {
            $query->where('tanggal_masuk', $this->tanggal);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Kategori',
            'Divisi',
            'Bagian',
            'Tanggal Masuk',
            'Fasilitas Loker',
        ];
    }

    public function map($karyawan): array
    {
        $statusLoker = $karyawan->penghuni
        ? ($karyawan->penghuni->kode_rak . ' - ' . $karyawan->penghuni->no_loker)
        : 'Belum / Tidak memiliki Loker';

        return [
            ' ' . $karyawan->nik,
            $karyawan->nama,
            $karyawan->jenis_kelamin == 'L' ? 'Laki-Laki' : ($karyawan->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
            $karyawan->staff == 'Y' ? 'Staff' : ($karyawan->staff == 'N' ? 'Non Staff' : '-'),
            $karyawan->kode_divisi,
            $karyawan->kode_bagian,
            $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d-m-Y') : '-',
            $statusLoker,
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
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
