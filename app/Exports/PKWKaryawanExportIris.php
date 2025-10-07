<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\PKWKaryawan;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PKWKaryawanExportIris implements WithColumnFormatting, FromArray
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
            "NIK", // A
            "NAMA", // B
            "Gender", // C
            "Address", // D
            "Phone", // E
            "Birthplace", // F
            "DOB(yyyy-mm-dd)", // G
            "Religion", // H
            "Marital", // I
            "Education", // J
            "COMPANY CODE", // K
            "TIPE KARYAWAN", // L
        ];
        $employees = PKWKaryawan::whereIn('id', explode(',', $this->ids))->orderByRaw('SUBSTR(nik, 8) asc')->get();
        foreach($employees as $employee) {
            if($employee->agama == 'islam') {
                $agama = 'C';
            }elseif($employee->agama == 'kristen') {
                $agama = 'A';
            }elseif($employee->agama == 'katolik') {
                $agama = 'B';
            }elseif($employee->agama == 'budha') {
                $agama = 'D';
            }elseif($employee->agama == 'hindu') {
                $agama = 'E';
            }elseif($employee->agama == 'kong hu cu') {
                $agama = 'F';
            }else{
                $agama = '';
            }

            if($employee->status_perdata == 'Menikah') {
                $status_perdata = 'M';
            }elseif($employee->status_perdata == 'Belum Menikah') {
                $status_perdata = 'S';
            }elseif($employee->status_perdata == 'Duda') {
                $status_perdata = 'D';
            }elseif($employee->status_perdata == 'Janda') {
                $status_perdata = 'J';
            }else{
                $status_perdata = '';
            }

            $data[] = [
                str_replace( "'"," ", $employee->nik),
                str_replace( "'"," ", ucwords(strtolower($employee->nama))),
                $employee->jenis_kelamin == 'L' ? 'M' : 'F',
                str_replace( "'"," ", $employee->alamat_ktp.', Kel. '.ucfirst(strtolower($employee->ktp_desa->name)).', Kec. '.ucfirst(strtolower($employee->ktp_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->ktp_kota->name)).', Provinsi '.ucfirst(strtolower($employee->ktp_provinsi->name)) ),
                str_replace( "'"," ", $employee->nomor_hp ),
                str_replace( "'"," ", ucwords(strtolower($employee->tempat_lahir)) ),
                $employee->tanggal_lahir,
                $agama,
                $status_perdata,
                $employee->pendidikan,
                'A005',
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
