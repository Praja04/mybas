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

class KaryawanKeluarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $data; // Hanya 1 parameter penangkap array keranjang

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // 1. Ekstrak NIK dari array keranjang
        // Catatan: Di fungsi checkout, NIK disimpan di array dengan key 'nik'
        $niks = collect($this->data)->pluck('nik')->filter()->toArray();

        // Pagar Betis: Kalau kosong, return Excel kosong
        if (empty($niks)) {
            return new Collection();
        }

        // 2. Tarik Database (Gak perlu relasi loker untuk Admin)
        return HrKaryawan::whereIn('nik', $niks)->get();
    }

    public function headings(): array
    {
        // Urutan disamakan dengan Tabel UI Checkout Admin + Kolom Alasan & Tgl
        return [
            'Nama',
            'NIK',
            'Divisi / Dept',
            'Kode Bagian',
            'Kode Admin',
            'Kode Group',
            'Alasan Keluar',
            'Tgl Keluar',
        ];
    }

    public function map($karyawan): array
    {
        // Cari alasan dan tanggal keluar dari array keranjang ($this->data)
        // karena data ini belum tentu ke-update instan di DB (tergantung antrean)
        $detailCart = collect($this->data)->firstWhere('nik', $karyawan->nik);

        $alasanKeluar = $detailCart['alasan_keluar'] ?? $karyawan->alasan_keluar ?? '-';
        $tglKeluar    = $detailCart['tanggal_keluar'] ?? $karyawan->tanggal_keluar;

        // Format tanggal biar enak dibaca HR
        $formattedTglKeluar = ($tglKeluar && $tglKeluar !== '0000-00-00')
            ? \Carbon\Carbon::parse($tglKeluar)->format('d-m-Y')
            : '-';

        return [
            $karyawan->nama,
            ' ' . $karyawan->nik, // Kasih spasi biar Excel gak ngehapus 0 di depan
            $karyawan->kode_divisi,
            $karyawan->kode_bagian,
            $karyawan->kode_admin,
            $karyawan->kode_group,
            $alasanKeluar,
            $formattedTglKeluar,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC3545'], // Warna Merah (Danger) khas Checkout
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            // NIK sekarang pindah ke kolom B, format jadi TEXT
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
