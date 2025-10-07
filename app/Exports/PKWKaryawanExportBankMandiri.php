<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\PKWKaryawan;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PKWKaryawanExportBankMandiri implements WithColumnFormatting, FromArray
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }
    
    public function array(): array
    {
        $data = [];
        $data[] = [
            "NAMA",
            "KELAMIN",
            "TGL_LHR",
            "KOTA_LHR",
            "NO_KTP",
            "EXP_KTP",
            "KOTA_KTP",
            "GELARSBL",
            "GELARSDH",
            "IBUKANDUNG",
            "ALAMAT1",
            "ALAMAT2",
            "KODEPOS",
            "CIF_NO",
            "PRODUK",
            "CURRENCY",
            "PEKERJAAN",
            "JABATAN",
            "EMPLOYER",
            "TGL_MULAI",
            "KODE_INDUSTRI",
            "GAJI",
            "PEN_LAIN",
            "TLP_RMH",
            "WARGANEGARA",
            "STS KAWIN",
            "TUJUAN BUKA REKENING",
            "BIAYA ADMIN KHUSUS",
            "KODE CABANG"
        ];

        $employees = PKWKaryawan::whereIn('id', explode(',', $this->ids))->get();
        foreach($employees as $key => $employee) {
            $data[] = [
                $employee->nama, // "NAMA",
                $employee->jenis_kelamin == 'L' ? 'M' : 'F', // "KELAMIN",
                explode('-', $employee->tanggal_lahir)[2].explode('-', $employee->tanggal_lahir)[1].explode('-', $employee->tanggal_lahir)[0], // "TGL_LHR",
                $employee->tempat_lahir, // "KOTA_LHR",
                $employee->nik_ktp.' ', // "NO_KTP",
                '000000', // "EXP_KTP",
                $employee->ktp_kota->name, // "KOTA_KTP",
                '', // "GELARSBL",
                '', // "GELARSDH",
                $employee->nama_ibu, // "IBUKANDUNG",
                $employee->alamat_ktp.', Kel. '.ucfirst(strtolower($employee->ktp_desa->name)).', Kec. '.ucfirst(strtolower($employee->ktp_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->ktp_kota->name)).', Provinsi '.ucfirst(strtolower($employee->ktp_provinsi->name)), // "ALAMAT1",
                $employee->alamat_sekarang.', Kel. '.ucfirst(strtolower($employee->sekarang_desa->name)).', Kec. '.ucfirst(strtolower($employee->sekarang_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->sekarang_kota->name)).', Provinsi '.ucfirst(strtolower($employee->sekarang_provinsi->name)), // "ALAMAT2",
                '', // "KODEPOS",
                '0', // "CIF_NO",
                '', // "PRODUK",
                'IDR', // "CURRENCY",
                'PSW', // "PEKERJAAN",
                '28', // "JABATAN",
                'PT. PRAKARSA ALAM SEGAR', // "EMPLOYER",
                explode('-', $employee->tanggal_masuk)[2].explode('-', $employee->tanggal_masuk)[1].substr(explode('-', $employee->tanggal_masuk)[0], -2), // "TGL_MULAI",
                '03', // "KODE_INDUSTRI",
                '0', // "GAJI",
                '0', // "PEN_LAIN",
                $employee->nomor_hp, // "TLP_RMH",
                '000', // "WARGANEGARA",
                $employee->status_perdata == 'Belum Menikah' ? 'B' : 'A', // "STS KAWIN",
                'A', // "TUJUAN BUKA REKENING",
                '', // "BIAYA ADMIN KHUSUS",
                '', // "KODE CABANG"
            ];
        }

        return $data;
    }

    public function columnFormats(): array
    {
        return [];
    }
}
