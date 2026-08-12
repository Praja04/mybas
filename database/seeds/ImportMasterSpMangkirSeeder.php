<?php

use Illuminate\Database\Seeder;
use App\SpKodePelanggaran;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMasterSpMangkirSeeder extends Seeder
{
    public function run()
    {
        $filePath = 'C:\\Users\\rajad\\Downloads\\Formula Master Kode Mangkir SP BAS.xlsx';
        if (!file_exists($filePath)) {
            $this->command->error("File Excel tidak ditemukan di path: {$filePath}");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray(null, true, true, true);

            $imported = 0;
            $updated = 0;

            foreach ($rows as $idx => $row) {
                if ($idx === 1) continue; // Skip header

                $kode = trim($row['A'] ?? '');
                $bentuk = trim($row['D'] ?? '');
                $dasar = trim($row['E'] ?? '');
                $jenisSpRaw = trim($row['F'] ?? '');

                if (empty($kode) || strtolower($kode) === 'kode') {
                    continue;
                }

                $jenisSp = $jenisSpRaw ?: 'SP I';

                $existing = SpKodePelanggaran::where('kode', $kode)
                    ->where('kategori_kode', 'MANGKIR')
                    ->first();

                if ($existing) {
                    $existing->update([
                        'nama_pelanggaran' => $kode,
                        'bentuk_pelanggaran' => $bentuk,
                        'dasar_pertimbangan' => $dasar,
                        'pasal_dilanggar' => $dasar,
                        'deskripsi' => $bentuk,
                        'jenis_sp' => $jenisSp,
                    ]);
                    $updated++;
                } else {
                    SpKodePelanggaran::create([
                        'kategori_kode' => 'MANGKIR',
                        'kode' => $kode,
                        'nama_pelanggaran' => $kode,
                        'bentuk_pelanggaran' => $bentuk,
                        'dasar_pertimbangan' => $dasar,
                        'pasal_dilanggar' => $dasar,
                        'deskripsi' => $bentuk,
                        'jenis_sp' => $jenisSp,
                    ]);
                    $imported++;
                }
            }

            echo "Import Master SP Mangkir Berhasil! Baru: {$imported}, Diperbarui: {$updated}\n";

        } catch (\Exception $e) {
            echo "Gagal mengimpor master mangkir: " . $e->getMessage() . "\n";
        }
    }
}
