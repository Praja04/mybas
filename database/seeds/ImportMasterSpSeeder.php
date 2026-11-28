<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMasterSpSeeder extends Seeder
{
    public function run()
    {
        $filePath = 'C:\\Users\\rajad\\Downloads\\kode master sp.xlsx';
        if (!file_exists($filePath)) {
            $this->command->error("Master file not found at: {$filePath}");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);

        // Truncate existing sp_kode_pelanggarans
        DB::table('sp_kode_pelanggarans')->truncate();

        $adminCount = 0;
        $irCount = 0;

        // 1. Process sheet: kode_admin
        $sheetAdmin = $spreadsheet->getSheetByName('kode_admin');
        if ($sheetAdmin) {
            $rows = $sheetAdmin->toArray(null, true, true, true);
            foreach ($rows as $idx => $row) {
                if ($idx === 1) continue; // Skip header

                $kodeAdmin = trim($row['A'] ?? '');
                $bentuk = trim($row['B'] ?? '');
                $dasar = trim($row['C'] ?? '');
                $tingkatSp = trim($row['D'] ?? '');

                if (empty($kodeAdmin) || strtolower($kodeAdmin) === 'kode_admin') continue;

                DB::table('sp_kode_pelanggarans')->insert([
                    'kategori_kode' => 'ADMIN',
                    'kode' => $kodeAdmin,
                    'nama_pelanggaran' => $kodeAdmin,
                    'bentuk_pelanggaran' => $bentuk,
                    'dasar_pertimbangan' => $dasar,
                    'pasal_dilanggar' => $dasar,
                    'deskripsi' => $bentuk,
                    'jenis_sp' => $tingkatSp ?: 'SP I',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $adminCount++;
            }
        }

        // 2. Process sheet: kode_ir
        $sheetIr = $spreadsheet->getSheetByName('kode_ir');
        if ($sheetIr) {
            $rows = $sheetIr->toArray(null, true, true, true);
            foreach ($rows as $idx => $row) {
                if ($idx === 1) continue; // Skip header

                $kodeIr = trim($row['A'] ?? '');
                $bentuk = trim($row['B'] ?? '');
                $dasar = trim($row['C'] ?? '');
                $tingkatSp = trim($row['D'] ?? '');

                if (empty($kodeIr) || strtolower($kodeIr) === 'kode_ir') continue;

                DB::table('sp_kode_pelanggarans')->insert([
                    'kategori_kode' => 'IR',
                    'kode' => $kodeIr,
                    'nama_pelanggaran' => $kodeIr,
                    'bentuk_pelanggaran' => $bentuk,
                    'dasar_pertimbangan' => $dasar,
                    'pasal_dilanggar' => $dasar,
                    'deskripsi' => $bentuk,
                    'jenis_sp' => $tingkatSp ?: 'SP I',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $irCount++;
            }
        }

        echo "SUCCESS: Imported {$adminCount} kode_admin entries and {$irCount} kode_ir entries!\n";
    }
}
