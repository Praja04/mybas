<?php
namespace App\Imports\HRConnect;

use App\HrKaryawan;
use App\Jobs\HRConnect\KaryawanMasukToHR;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmKaryawanMasuk implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $niks = $rows->pluck('nik')->filter()->toArray();

        DB::beginTransaction();

        try {
            $karyawans = HrKaryawan::whereIn('nik', $niks)
                ->lockForUpdate()
                ->get()
                ->keyBy('nik');

            $processedData = [];

            foreach ($rows as $row) {
                $nik = $row['nik'];

                if (! isset($karyawans[$nik])) {
                    Log::warning("Karyawan dengan NIK {$nik} tidak ditemukan");
                    continue;
                }

                $karyawan = $karyawans[$nik];

                $kode_bagian   = $row['kode_bagian'] ?? null;
                $kode_group    = $row['kode_group'] ?? null;
                $kode_admin    = $row['kode_admin'] ?? null;
                $status_proses = strtoupper(trim($row['proses'])) ?? '';

                $p_in = $status_proses == "IN" ? "Y" : "N";
                $p_no = $status_proses == "NO-IN" ? "Y" : "N";

                $updateData = [
                    'in_kode_group' => 'Y',
                    'p_in'          => $p_in,
                    'p_no'          => $p_no,
                ];

                if ($status_proses === 'IN') {
                    $updateData['kode_group']  = $kode_group;
                    $updateData['kode_admin']  = $kode_admin;
                    $updateData['kode_bagian'] = $kode_bagian;
                } else {
                    $updateData['kode_group'] = null;
                    $updateData['kode_admin'] = null;
                }

                $karyawan->update($updateData);

                $processedData[] = [
                    'idCheckwish' => $karyawan->id,
                ];
            }

            $to = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_in');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            DB::commit();

            if (! empty($processedData) && ! empty($to)) {
                KaryawanMasukToHR::dispatch($to, $processedData);
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal Import Karyawan Masuk: ', $e->getMessage());

            throw $e;
        }
    }

    // protected $processedData = [];

    // public function model(array $row)
    // {
    //     try {
    //         $karyawan = HrKaryawan::where('nik', $row['nik'])->first();
    //         if ($karyawan) {
    //             $kode_bagian = $row['kode_bagian'];
    //             $kode_group = $row['kode_group'];
    //             $kode_admin = $row['kode_admin'];
    //             $status_proses = $row['proses'];
    //             $p_in = $status_proses == "IN" ? "Y" : "N";
    //             $p_no = $status_proses == "NO-IN" ? "Y" : "N";

    //             $isNoProcess = [
    //                 // 'in_complete' => 'Y', // Paralel sama GA
    //                 'in_kode_group' => 'Y',
    //                 'p_in' => $p_in,
    //                 'p_no' => $p_no,
    //                 // 'tanggal_masuk' => NULL
    //             ];

    //             $isProcess = [
    //                 'kode_group' => $kode_group,
    //                 'kode_admin' => $kode_admin,
    //                 'kode_bagian' => $kode_bagian,
    //                 // 'in_complete' => 'Y', // Paralel sama GA
    //                 'in_kode_group' => 'Y',
    //                 'p_in' => $p_in,
    //                 'p_no' => $p_no,
    //                 // 'tanggal_masuk' => Carbon::parse(now())->format('Y-m-d')
    //             ];

    //             $status_proses == "NO-IN" ? HrKaryawan::where('nik', $row['nik'])->update($isNoProcess) : HrKaryawan::where('nik', $row['nik'])->update($isProcess);

    //             $this->processedData[] = [
    //                 'idCheckwish' => $karyawan->id,
    //             ];
    //         }
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }

    //     return null;
    // }

    // public function __destruct()
    // {
    //     $email_hr_karyawan = User::whereHas('group.permissions', function ($query) {
    //         $query->where('codename', 'hr_connect_notified_in');
    //     })->select('email')
    //     ->whereNotNull('email')
    //     ->groupBy('email')
    //     ->get();

    //     $to = $email_hr_karyawan->pluck('email')->toArray();

    //     if (!empty($this->processedData)) {
    //         KaryawanMasukToHR::dispatch($to, $this->processedData);
    //     }
    // }
}
