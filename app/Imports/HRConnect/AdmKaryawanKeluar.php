<?php
namespace App\Imports\HRConnect;

use App\HrKaryawan;
use App\Jobs\HRConnect\KaryawanKeluarToGA;
use App\Jobs\HRConnect\NotifiedOut;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AdmKaryawanKeluar implements ToCollection, WithHeadingRow
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

                $karyawan       = $karyawan[$nik];
                $tanggal_keluar = $row['tanggal_keluar'];

                if (is_numeric($tanggal_keluar)) {
                    $tanggal_keluar = Date::excelToDateTimeObject($tanggal_keluar)->format('Y-m-d');
                } else {
                    $tanggal_keluar = Carbon::parse($tanggal_keluar)->format('Y-m-d');
                }

                $karyawan->update([
                    'tgl_shift_out'  => now()->format('Y-m-d'),
                    'is_excuse_out'  => 'Y',
                    'alasan_keluar'  => $row['alasan_keluar'] ?? 'Checkout Admin',
                    'tanggal_keluar' => $tanggal_keluar,
                ]);

                $processedData[] = [
                    'nik'            => $karyawan->nik,
                    'nama'           => $karyawan->nama,
                    'dept'           => $karyawan->kode_divisi,
                    'kode_bagian'    => $karyawan->kode_bagian,
                    'alasan_keluar'  => $row['alasan_keluar'] ?? 'Checkout Admin',
                    'tanggal_keluar' => $tanggal_keluar,
                ];
            }

            $email_ga = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ga');
            })->whereNotNull('email')->pluck('email')->unique()->toArray();

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_out');
            })->whereNotNull('email')->pluck('email')->unique()->toArray();

            DB::commit();

            if (! empty($processedData)) {
                if (! empty($email_hr)) {
                    NotifiedOut::dispatch($email_hr, $processedData);
                }

                if (! empty($email_ga)) {
                    $paketKirim = [
                        'list_karyawan' => $processedData,
                        'tautan'        => route('ga.karyawan-keluar'),
                    ];

                    KaryawanKeluarToGA::dispatch($email_ga, $paketKirim);
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal Import Karyawan Keluar: ', $e->getMessage());

            throw $e;
        }
    }
}
