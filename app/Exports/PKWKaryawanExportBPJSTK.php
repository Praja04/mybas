<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\PKWKaryawan;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PKWKaryawanExportBPJSTK implements WithColumnFormatting, FromArray, WithMultipleSheets
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
            'NO_PEGAWAI', // A
            'NAMA_DEPAN', // B
            'NAMA_TENGAH', // C
            'NAMA_BELAKANG', // D
            'GELAR', // E
            'TELEPON_AREA_RUMAH', // F
            'TELEPON_RUMAH', // G
            'TELEPON_AREA_KANTOR', // H
            'TELEPON_KANTOR', // I
            'TELEPON_EXT_KANTOR', // J
            'HP', // K
            'EMAIL', // L
            'TEMPAT_LAHIR', // M
            'TANGGAL_LAHIR', //DD-MM-YYYY // N
            'NAMA_IBU_KANDUNG', // O
            'JENIS_IDENTITAS', //KTP // P
            'NOMOR_IDENTITAS', // Q
            'MASA_LAKU_IDENTITAS', //01-01-2090 // R
            'JENIS KELAMIN', // S
            'SURAT_MENYURAT_KE', //S // T
            'TANGGAL_KEPESERTAAN', // U
            'STATUS_KAWIN', // T/K // V
            'GOLONGAN_DARAH', //O // W
            'NPWP', // X
            'KODE_NEGARA', //ID // Y
            'UPAH', // '' // Z
            'ALAMAT', // AA
            'KODE_POS', // 17131 // AB
            'LOKASI_PEKERJAAN', // 3275 // AC
        ];

        $employees = PKWKaryawan::whereIn('id', explode(',', $this->ids))->get();
        foreach($employees as $key => $employee) {
            $data[] = [
                $employee->nik, // A
                $employee->nama, // B
                '', // C
                '', // D
                '', // E
                '', // F
                '', // G
                '', // H
                '', // I
                '', // J
                $employee->nomor_hp, // K
                $employee->email, // L
                $employee->tempat_lahir, // M
                explode('-', $employee->tanggal_lahir)[2].'-'.explode('-', $employee->tanggal_lahir)[1].'-'.explode('-', $employee->tanggal_lahir)[0], //DD-MM-YYYY // N
                $employee->nama_ibu, // O
                'KTP', //KTP // P
                str_replace("'", ' ', $employee->nik_ktp."'"), // Q
                '01-01-2090', //01-01-2090 // R
                $employee->jenis_kelamin, // S
                'S', //S // T
                '', // U
                $employee->status_perdata == 'Belum Menikah' ? 'T' : 'K', // T/K // V
                $employee->golongan_darah, //O // W
                $employee->no_npwp.' ', // X
                'ID', //ID // Y
                '', // '' // Z
                $employee->alamat_ktp.', Kel. '.ucfirst(strtolower($employee->ktp_desa->name)).', Kec. '.ucfirst(strtolower($employee->ktp_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->ktp_kota->name)).', Provinsi '.ucfirst(strtolower($employee->ktp_provinsi->name)), // AA
                '17131', // 17131 // AB
                '3275', // 3275 // AC
            ];
        }

        return $data;
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new PKWKaryawanExportBPJSTKSheet1();
        $sheets[] = new PKWKaryawanExportBPJSTKSheet2($this->ids);
        $sheets[] = new PKWKaryawanExportBPJSTKSheet3();
        return $sheets;
    }
}
