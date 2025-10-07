<?php

namespace App\Imports\HRConnect;

use App\User;
use Carbon\Carbon;
use App\HrKaryawan;
use App\HrGoodieApd;
use App\Jobs\HRConnect\NotifiedOut;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Jobs\HRConnect\KaryawanMasukToHR;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Jobs\HRConnect\KaryawanKeluarToGA;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmKaryawanKeluar implements ToModel, WithHeadingRow
{
    protected $processedData = [];

    public function model(array $row)
    {
        try {
            $karyawan = HrKaryawan::where('nik', $row['nik'])->first();
            if ($karyawan) {
                $tanggal_keluar = $row['tanggal_keluar'];

                if (is_numeric($tanggal_keluar)) {
                    $tanggal_keluar = Date::excelToDateTimeObject($tanggal_keluar)->format('Y-m-d');
                }

                $karyawan->update([
                    'tgl_shift_out' => date('Y-m-d'),
                    'is_excuse_out' => 'Y',
                    'alasan_keluar' => $row['alasan_keluar'],
                    'tanggal_keluar' => $tanggal_keluar
                ]);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        
        return null; 
    }

    public function __destruct()
    {
        $email_ga = User::whereHas('group.permissions', function ($query) {              
            $query->where('codename', 'hr_connect_ga');  
        })->select('email')
        ->whereNotNull('email')
        ->groupBy('email')
        ->get(); 
        
        $link = url('/hr-connect/dept-ga/karyawan-keluar');
        $to_ga = $email_ga->pluck('email')->toArray();
        // KaryawanKeluarToGA::dispatch($to_ga, $link); 
        
        // Reminder besok nya jam 09:00 
        $reminder = Carbon::tomorrow()->setTime(9, 0, 0); 
        // KaryawanKeluarToGA::dispatch($to_ga, $link)->delay($reminder);
        
        $email_hr_karyawan = User::whereHas('group.permissions', function ($query) {
            $query->where('codename', 'hr_connect_notified_out');
        })->select('email')
        ->whereNotNull('email')
        ->groupBy('email')
        ->get();
        
        $to = $email_hr_karyawan->pluck('email')->toArray();

        if (!empty($this->processedData)) {
            NotifiedOut::dispatch($to, $this->processedData);
        }
    }
}