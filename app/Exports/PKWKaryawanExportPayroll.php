<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\PKWKaryawan;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PKWKaryawanExportPayroll implements WithColumnFormatting, FromArray
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
            "Nip", // A
            "Nama", // B
            "Alamat", // C
            "No Telp", // D
            "Pengalaman", // E
            "Tempat Lahir", // F
            "Tgl Lahir", // G
            "Agama", // H
            "Jenis Kelamin", // I
            "Status Nikah", // J
            "Jumlah Anak", // K
            "Status PPh21", // L
            "Pendidikan", // M
            "Kode Divisi", // N
            "Kode Bagian", // O
            "Kode Jabatan", // P
            "Kode Group", // Q
            "Kode Admin", // R
            "Kode Periode", // S
            "Kode Kontrak", // T
            "Tgl Masuk", // U
            "Tgl Keluar", // V
            "Aktif", // W
            "Gaji per Bulan", // X
            "Jumlah SP", // Y
            "No KPJ", // Z
            "No HLD", // AA
            "Astek", // AB
            "Kode Wings", // AC
            "No Rekening", // AD
            "No KTP", // AE
            "Jari Bermasalah", // AF
            "Catatan", // AG
            "NPWP", // AH
            "Email", // AI
            "Begda", // AJ
            "Endda", // AK
            "User", // AL
            "Business Area",
            "Kelompok JKK",
            "Alamat 2",
            "Alamat 3",
            "Alasan Keluar",
            "Journal Group",
            "Loan",
            "OverTime",
            "Level Karyawan",
            "Sapuser",
            "Plant",
            "Tipekaryawan",
        ];
        $employees = PKWKaryawan::whereIn('id', explode(',', $this->ids))->orderByRaw('SUBSTR(nik, 8) asc')->get();
        foreach($employees as $employee) {
            $data[] = [
                str_replace( "'"," ", $employee->nik),
                str_replace( "'"," ", ucwords(strtolower($employee->nama))),
                str_replace( "'"," ", $employee->alamat_ktp.', Kel. '.ucfirst(strtolower($employee->ktp_desa->name)).', Kec. '.ucfirst(strtolower($employee->ktp_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->ktp_kota->name)).', Provinsi '.ucfirst(strtolower($employee->ktp_provinsi->name)) ),
                str_replace( "'"," ", $employee->nomor_hp ),
                str_replace( "'"," ", ""), // "Pengalaman",
                str_replace( "'"," ", ucwords(strtolower($employee->tempat_lahir)) ),
                str_replace( "'"," ", explode('-', $employee->tanggal_lahir)[0].explode('-', $employee->tanggal_lahir)[1].explode('-', $employee->tanggal_lahir)[2] ),
                str_replace( "'"," ", ucfirst($employee->agama) ),
                str_replace( "'"," ", $employee->jenis_kelamin ),
                str_replace( "'"," ", $employee->status_perdata ),
                str_replace( "'"," ", '0' ),
                str_replace( "'"," ", "TK0" ),
                str_replace( "'"," ", $employee->pendidikan ),// "Pendidikan",
                str_replace( "'"," ", $employee->divisi->kode_divisi ),
                str_replace( "'"," ", $employee->bagian->kode_bagian ),
                str_replace( "'"," ", $employee->jabatan->kode_jabatan ),
                str_replace( "'"," ", $employee->group->kode_group ),
                str_replace( "'"," ", $employee->admin->kode_admin ),
                str_replace( "'"," ", "Bulanan" ),
                str_replace( "'"," ", "Non Staff Kontrak" ),
                str_replace( "'"," ", explode('-', $employee->tanggal_masuk)[0].explode('-', $employee->tanggal_masuk)[1].explode('-', $employee->tanggal_masuk)[2] ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "1" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "0" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "1" ), // "Astek"
                str_replace( "'"," ", "" ), // "Kode Wings",
                str_replace( "'"," ", $employee->nomor_rekening_bank ),
                str_replace( "'"," ", $employee->nik_ktp."'" ),
                str_replace( "'"," ", "0" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", $employee->no_npwp ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", $this->getBegda($employee->tanggal_masuk) ), // Begda
                str_replace( "'"," ", "99981231" ),
                str_replace( "'"," ", $employee->admin->kode_admin.", henry, khabib, dwi, support, aris, joko, HRRecruitment,desygand,herdianto,iradmin,alberto,admincovid" ),
                str_replace( "'"," ", "AE01" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", $employee->alamat_sekarang.', Kel. '.ucfirst(strtolower($employee->sekarang_desa->name)).', Kec. '.ucfirst(strtolower($employee->sekarang_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->sekarang_kota->name)).', Provinsi '.ucfirst(strtolower($employee->sekarang_provinsi->name)) ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "PNonStaff" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "1" ),
                str_replace( "'"," ", "" ),
                str_replace( "'"," ", "1001" ),
                str_replace( "'"," ", "N" ),
            ];
        }

        return $data;
    }

    private function getBegda($tanggal)
    {
        if((int)explode('-',$tanggal)[2] <= 20) {
            $month = date('m');
            $month = (int)$month-1;
            $year = date('Y');
            if($month == 0) {
                $year = (int)$year-1;
                $month = 12;
            }
            if($month <= 9) {
                $month = '0'.$month;
            }
            return $year.$month.'21';
        }else{
            $month = date('m');
            if($month <= 9) {
                $month = '0'.$month;
            }
            return date('Y').$month.'01';
        }
    }

    public function columnFormats(): array
    {
        return [
            'AE' => '#0',
            'AH' => '#0',
            'AD' => '#0'
        ];
    }
}
