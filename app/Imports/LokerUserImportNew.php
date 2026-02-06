<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class LokerUserImportNew implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 2; // HEADER ADA DI ROW 2
    }

    public function collection(Collection $rows)
    {
        $service = app(\App\Services\LokerAssignmentService::class);

        foreach ($rows as $i => $row) {
            $excelRow = $i + 3;

            try {
                $service->assign([
                    'nik' => $row['nik_karyawan'],
                    'nama' => $row['nama_karyawan'],
                    'divisi' => $row['dept'],
                    'jk' => strtoupper($row['jenis_kelamin']) === 'P' ? 'L' : 'P',
                    'no_loker' => (int) $row['no'],
                    'staff' => strtolower(str_replace(' ', '_', $row['karyawan'])),
                ]);
            } catch (\Throwable $e) {
                throw new \Exception("Error pada baris ke-{$excelRow}: " . $e->getMessage());
            }
        }
        // try {
        // foreach ($rows as $i => $row) {
        //     // dd($row);
        //     // skip baris kosong
        //     if (empty($row['nik_karyawan'])) {
        //         continue;
        //     }

        //     // ===== NORMALISASI DATA =====
        //     $jkExcel = strtoupper(trim($row['jenis_kelamin'])); // P / W

        //     if (!in_array($jkExcel, ['P', 'W'])) {
        //         throw new \Exception('JK tidak valid di baris ' . ($i + 3));
        //     }

        //     // mapping ke DB
        //     $jk = $jkExcel === 'P' ? 'L' : 'P'; // P=Pria(L), W=Wanita(P)

        //     $staff = strtolower(str_replace(' ', '_', trim($row['karyawan'])));

        //     if (!in_array($staff, ['staff', 'non_staff', 'mitra_kerja'])) {
        //         throw new \Exception('Kategori karyawan tidak valid di baris ' . ($i + 3));
        //     }

        //     $kodeRakUtama = $jk === 'L' ? 'PB' : 'WB';
        //     $kodeRakPair = $jk === 'L' ? 'PS' : 'WS';

        //     $noLoker = (int) $row['no'];

        //     // ===== INSERT RAK BAJU =====
        //     DB::table('loker_penghuni')->insert([
        //         'nik' => $row['nik_karyawan'],
        //         'nama' => $row['nama_karyawan'],
        //         'divisi' => $row['dept'],
        //         'jk' => $jk,
        //         'kode_rak' => $kodeRakUtama,
        //         'no_loker' => $noLoker,
        //         'staff' => $staff,
        //         'is_active' => 'Y',
        //         'tgl_masuk' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);

        //     // ===== INSERT RAK SEPATU =====
        //     DB::table('loker_penghuni')->insert([
        //         'nik' => $row['nik_karyawan'],
        //         'nama' => $row['nama_karyawan'],
        //         'divisi' => $row['dept'],
        //         'jk' => $jk,
        //         'kode_rak' => $kodeRakPair,
        //         'no_loker' => $noLoker,
        //         'staff' => $staff,
        //         'is_active' => 'Y',
        //         'tgl_masuk' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
        // } catch (Throwable $e) {
        //     throw $e;
        // }
    }
}
