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

class GaShiftOut implements ToModel, WithHeadingRow
{
    protected $processedData = [];

    public function model(array $row)
    {
        try {
            $karyawan = HrKaryawan::where('nik', $row['nik'])->first();
            if ($karyawan) {
                $nik = $row['nik'];
                $alasan_keluar = $row['alasan_keluar'];

                $sebelumnya = \DB::table('loker_master_user')
                    ->where('nik', $nik)->first();

                if($sebelumnya){
                    $data = [
                        'nik' => $sebelumnya->nik,
                        'no_loker' => $sebelumnya->no_loker,
                        'status' => 'OUT',
                        'keterangan' => 'Sudah Keluar',
                        'nama_pengisi' => auth()->user()->name ?? '',
                        'tgl_pengisi' => date('Y-m-d'),
                        'nik_pengisi' => auth()->user()->username ?? '',
                        'jam_pengisi' => date('H:i:s'),
                        'penghuni_sebelumnya' => $sebelumnya->nama,
                        'alasan' => $alasan_keluar,
                        'kode_area' => $sebelumnya->kode_area,
                        'kode_blok' => $sebelumnya->kode_blok
                    ];
    
                    \DB::table('loker_user_transaksi')->insert($data);
                    
                    \DB::table('loker_master_nomer')
                        ->where([
                            'kode_blok' => $sebelumnya->kode_blok,
                            'no_loker' => $sebelumnya->no_loker,
                            'kode_area' => $sebelumnya->kode_area
                        ])->update(['status' => 0]);
    
                    // Senin tanya ke mas her soal ini!
                    \DB::table('loker_master_user')->where('nik', $nik)
                            ->update([
                                'nik' => '',
                                'nama' => ''
                            ]);
                }

                HrKaryawan::where('id', $karyawan->id)
                        ->update([
                            'out_complete' => $row['status'] == '1' ? 'Y' : 'N'
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
            $query->where('codename', 'hr_connect_notified_out');
        })->select('email')
        ->whereNotNull('email')
        ->groupBy('email')
        ->get();
        
        $to = $email_hr_karyawan->pluck('email')->toArray();
        $link = url('/hr-connect/dept-ga/karyawan-keluar');
        if (!empty($this->processedData)) {
            KaryawanKeluarSelesaiToHR::dispatch($to, $this->processedData, $link);
        }
    }
}