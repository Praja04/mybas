<?php

namespace App\Imports\HRConnect;

use App\User;
use Carbon\Carbon;
use App\HrKaryawan;
use App\HrGoodieApd;
use App\Jobs\HRConnect\KaryawanKeluarSelesaiToHR;
use App\Jobs\HRConnect\NotifiedOut;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Jobs\HRConnect\KaryawanMasukToHR;
use App\Jobs\HRConnect\KaryawanKeluarToGA;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HrdIrKaryawanKeluar implements ToModel, WithHeadingRow
{
    protected $processedData = [];

    public function model(array $row)
    {
        try {
            $karyawan = HrKaryawan::where('nik', $row['nik'])->first();
            if ($karyawan) {
                HrKaryawan::where('id', $karyawan->id)
                        ->update([
                            'checked_ir' => $row['status'] == '1' ? 'Y' : 'N'
                        ]);

                $this->processedData[] = [
                    'checklistId' => $karyawan->id,
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
            $query->where('codename', 'hr_connect_ir');
        })->select('email')
        ->whereNotNull('email')
        ->groupBy('email')
        ->get();
        
        $to = $email_hr_karyawan->pluck('email')->toArray();
        $link = url('/hr-connect/dept-hrd/karyawan-keluar');
        if (!empty($this->processedData)) {
            KaryawanKeluarSelesaiToHR::dispatch($to, $this->processedData, $link);
        }
    }
}