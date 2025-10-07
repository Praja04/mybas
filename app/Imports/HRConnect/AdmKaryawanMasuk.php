<?php

namespace App\Imports\HRConnect;

use App\User;
use Carbon\Carbon;
use App\HrKaryawan;
use App\HrGoodieApd;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Jobs\HRConnect\KaryawanMasukToHR;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmKaryawanMasuk implements ToModel, WithHeadingRow
{
    protected $processedData = [];

    public function model(array $row)
    {
        try {
            $karyawan = HrKaryawan::where('nik', $row['nik'])->first();
            if ($karyawan) {
                $kode_bagian = $row['kode_bagian'];
                $kode_group = $row['kode_group'];
                $kode_admin = $row['kode_admin'];
                $status_proses = $row['proses'];
                $p_in = $status_proses == "IN" ? "Y" : "N";
                $p_no = $status_proses == "NO-IN" ? "Y" : "N";

                $isNoProcess = [
                    // 'in_complete' => 'Y', // Paralel sama GA
                    'in_kode_group' => 'Y',
                    'p_in' => $p_in,
                    'p_no' => $p_no,
                    // 'tanggal_masuk' => NULL
                ];

                $isProcess = [
                    'kode_group' => $kode_group,
                    'kode_admin' => $kode_admin,
                    'kode_bagian' => $kode_bagian,
                    // 'in_complete' => 'Y', // Paralel sama GA
                    'in_kode_group' => 'Y',
                    'p_in' => $p_in,
                    'p_no' => $p_no,
                    // 'tanggal_masuk' => Carbon::parse(now())->format('Y-m-d')
                ];

                $status_proses == "NO-IN" ? HrKaryawan::where('nik', $row['nik'])->update($isNoProcess) : HrKaryawan::where('nik', $row['nik'])->update($isProcess);

                $this->processedData[] = [
                    'idCheckwish' => $karyawan->id,
                ];
            }
        } catch (\Exception $e) {
            throw $e;
        }
        
        return null; 
    }

    public function __destruct()
    {
        $email_hr_karyawan = User::whereHas('group.permissions', function ($query) {
            $query->where('codename', 'hr_connect_notified_in');
        })->select('email')
        ->whereNotNull('email')
        ->groupBy('email')
        ->get();
        
        $to = $email_hr_karyawan->pluck('email')->toArray();

        if (!empty($this->processedData)) {
            KaryawanMasukToHR::dispatch($to, $this->processedData);
        }
    }
}