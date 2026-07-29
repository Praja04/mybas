<?php

namespace App\Jobs\HR;

use App\Models\HR\HrMasterEmployee;
use App\Models\HR\HrMasterEmployeeBatch;
use App\Models\HR\HrMasterEmployeeStaging;
use App\Support\HrEmployeeNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessHrMasterEmployeeImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $batchId;
    protected $filePath;
    protected $username;
    protected $mitraKerjaChoice;
    protected $chunkSize = 500;
    protected $startRow = 3;

    protected const NIK_APOSTROPHE_WHEN_INACTIVE = [
        '17000069',
        '17000129',
    ];

    public function __construct($batchId, $filePath, $username, $mitraKerjaChoice = null)
    {
        $this->batchId = $batchId;
        $this->filePath = $filePath;
        $this->username = $username;
        $this->mitraKerjaChoice = $mitraKerjaChoice;
    }

    public function handle()
    {
        $batch = HrMasterEmployeeBatch::where('batch_id', $this->batchId)->first();
        if (! $batch) {
            return;
        }

        try {
            $batch->update(['status' => 'processing']);

            // Bersihkan staging dari batch sebelumnya milik user ini (yang belum dikonfirmasi)
            // agar NIK tidak konflik dan perbandingan "created/updated" konsisten
            HrMasterEmployeeStaging::where('send_by_username', $this->username)
                ->whereIn('status', ['created', 'updated'])
                ->delete();

            $rows = $this->readCsvRows($this->filePath);

            $batch->total_data = count($rows);
            $batch->save();

            if (empty($rows)) {
                $batch->update(['status' => 'completed']);
                $this->deleteUploadedFile();
                return;
            }

            // Bandingkan dengan table master (data yang sudah dikonfirmasi)
            $existingNikSet = $this->getExistingNikSet(array_column($rows, 'NIK'));

            $created = 0;
            $updated = 0;
            $chunks = array_chunk($rows, $this->chunkSize);

            foreach ($chunks as $chunk) {
                $payloads = [];
                $now = now();
                foreach ($chunk as $row) {
                    $isUpdate = isset($existingNikSet[$row['NIK']]);

                    $payloads[] = [
                        'NIK'              => $row['NIK'],
                        'Nama'             => $row['Nama'],
                        'Tgl Lahir'        => $row['Tgl Lahir'],
                        'Tgl Masuk'        => $row['Tgl Masuk'],
                        'Departmen'        => $row['Departmen'],
                        'Sub Departmen'    => $row['Sub Departmen'],
                        'Section'          => $row['Section'],
                        'Tipe Karyawan'    => $row['Tipe Karyawan'],
                        'Jabatan'          => $row['Jabatan'],
                        'Level'            => $row['Level'],
                        'PWS'              => $row['PWS'],
                        'Jenis Kelamin'    => $row['Jenis Kelamin'],
                        'Work Status'      => $row['Work Status'],
                        'Status Nikah'     => $row['Status Nikah'],
                        'Aktif'            => $row['Aktif'],
                        'Valid From'       => $row['Valid From'],
                        'send_by_username' => $this->username,
                        'batch_id'         => $this->batchId,
                        'status'           => $isUpdate ? 'updated' : 'created',
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];

                    if ($isUpdate) {
                        $updated++;
                    } else {
                        $created++;
                    }
                }

                $this->upsertChunk($payloads);
                unset($payloads, $chunk);
            }

            $batch->update([
                'created_count' => $created,
                'updated_count' => $updated,
                'status'        => 'completed',
            ]);

            // Hapus file upload setelah berhasil diproses
            $this->deleteUploadedFile();
        } catch (\Throwable $e) {
            $batch->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Tetap hapus file meskipun gagal
            $this->deleteUploadedFile();
            throw $e;
        }
    }

    protected function deleteUploadedFile(): void
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    protected function readCsvRows(string $path): array
    {
        if (! file_exists($path)) {
            throw new \RuntimeException("File tidak ditemukan: {$path}");
        }

        $fh = fopen($path, 'r');
        if ($fh === false) {
            throw new \RuntimeException("Gagal membuka file: {$path}");
        }

        $delimiter = $this->detectDelimiter($fh);
        rewind($fh);

        $rows = [];
        $rowNumber = 0;

        while (($data = fgetcsv($fh, 0, $delimiter)) !== false) {
            $rowNumber++;

            if ($rowNumber < $this->startRow) {
                continue;
            }

            $nik = isset($data[1]) ? trim((string) $data[1]) : '';
            if ($nik === '') {
                continue;
            }

            // Mode mitra_kerja: CSV TANPA kolom PWS (PWS diambil dari kolom Group index 13).
            // Mode normal:       CSV DENGAN kolom PWS di antara Level dan Payroll Type.
            $colOffset = $this->mitraKerjaChoice ? 0 : 1;

            // Hanya Payroll Type = Bulanan yang masuk ke staging / master.
            $payrollType = $this->str($data, 16 + $colOffset);
            if ($payrollType === null || strcasecmp($payrollType, 'Bulanan') !== 0) {
                continue;
            }

            $aktif         = $this->str($data, 22 + $colOffset);
            $tipeKaryawan  = $this->str($data, 11);

            // Untuk NIK tertentu dengan status Aktif = N dan Tipe Karyawan = Non Staff,
            // tambahkan tanda kutip (') di akhir NIK agar menjadi unik
            // ketika ada record dengan NIK sama berstatus Aktif = Y / Staff.
            $isListedNik = in_array($nik, self::NIK_APOSTROPHE_WHEN_INACTIVE, true);
            $isInactive  = $aktif === 'N';
            $isNonStaff  = $tipeKaryawan !== null && strcasecmp($tipeKaryawan, 'Non Staff') === 0;

            if ($isListedNik && $isInactive && $isNonStaff) {
                $nik = $nik . "'";
            }

            // Template CSV (baris 2 header) — mode normal (dengan kolom PWS):
            // 0:Company, 1:NIK, 2:Nama, 3:Tempat Lahir, 4:Tgl Lahir, 5:Tgl Masuk,
            // 6:Divisi, 7:Bus Area, 8:Sales Office, 9:Departmen, 10:Section,
            // 11:Tipe Karyawan, 12:Jabatan, 13:Group, 14:Sub Group, 15:Level,
            // 16:PWS, 17:Payroll Type, 18:Jenis Kelamin, 19:Alamat KTP, 20:Jumlah Anak,
            // 21:Work Status, 22:Status Nikah, 23:Aktif, 24:Valid From, 25:Valid To, 26:View
            //
            // Mode mitra_kerja: sama tanpa kolom PWS (Level=15, Payroll Type=16, ...).
            // Departmen dipakai langsung apa adanya (TANPA submapping PAS — beda PT)
            // Sub Departmen di-resolve dari Section via HrEmployeeNormalizer::resolveSubDeptBySection()
            $departmenRaw = $this->str($data, 9);
            $departmen    = $departmenRaw !== null ? HrEmployeeNormalizer::removePasPrefix($departmenRaw) : null;
            $section      = $this->str($data, 10);
            $subDepartmen = HrEmployeeNormalizer::resolveSubDeptBySection($section);

            $rows[] = [
                'NIK'             => $nik,
                'Nama'            => $this->str($data, 2),
                'Tgl Lahir'       => HrEmployeeNormalizer::normalizeDate($data[4] ?? null),
                'Tgl Masuk'       => HrEmployeeNormalizer::normalizeDate($data[5] ?? null),
                'Departmen'       => $departmen,
                'Sub Departmen'   => $subDepartmen,
                'Section'         => $section,
                'Tipe Karyawan'   => $this->mitraKerjaChoice ?: $this->str($data, 11),
                'Jabatan'         => $this->str($data, 12),
                'Level'           => $this->str($data, 15),
                'PWS'             => $this->mitraKerjaChoice ? $this->str($data, 13) : $this->str($data, 16),
                'Jenis Kelamin'   => $this->str($data, 17 + $colOffset),
                'Work Status'     => $this->str($data, 20 + $colOffset),
                'Status Nikah'    => $this->str($data, 21 + $colOffset),
                'Aktif'           => $this->str($data, 22 + $colOffset),
                'Valid From'      => HrEmployeeNormalizer::normalizeDate($data[23 + $colOffset] ?? null),
            ];
        }

        fclose($fh);
        return $rows;
    }

    protected function detectDelimiter($fh): string
    {
        $firstLine = fgets($fh);
        if ($firstLine === false) {
            return ',';
        }
        $candidates = [',', ';', "\t", '|'];
        $best = ',';
        $bestCount = 0;
        foreach ($candidates as $d) {
            $count = substr_count($firstLine, $d);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $d;
            }
        }
        return $best;
    }

    protected function str(array $row, $index): ?string
    {
        if (! isset($row[$index])) {
            return null;
        }
        $value = trim((string) $row[$index]);
        return $value === '' ? null : $value;
    }

    protected function getExistingNikSet(array $niks): array
    {
        if (empty($niks)) {
            return [];
        }

        // Bandingkan dengan master (data terkonfirmasi) saja
        // Staging sudah dibersihkan di awal handle()
        $existing = HrMasterEmployee::whereIn('NIK', $niks)->pluck('NIK')->all();

        return array_flip($existing);
    }

    protected function upsertChunk(array $payloads): void
    {
        if (empty($payloads)) {
            return;
        }

        $update = [
            'Nama', 'Tgl Lahir', 'Tgl Masuk', 'Departmen', 'Sub Departmen',
            'Section', 'Tipe Karyawan', 'Jabatan', 'Level', 'PWS', 'Jenis Kelamin', 'Work Status',
            'Status Nikah', 'Aktif', 'Valid From', 'send_by_username',
            'batch_id', 'status', 'updated_at',
        ];

        // Laravel 7.x tidak punya Query Builder::upsert() (baru di 8.x+).
        // newpas punya package "staudenmeir/laravel-upsert" untuk polyfill — mybas tidak.
        // Pakai raw MySQL: INSERT ... ON DUPLICATE KEY UPDATE
        $columns = array_keys($payloads[0]);
        $colList = '`' . implode('`, `', $columns) . '`';

        $updateParts = [];
        foreach ($update as $col) {
            $updateParts[] = '`' . $col . '` = VALUES(`' . $col . '`)';
        }
        $updateSql = implode(', ', $updateParts);

        $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $valuesPlaceholders = implode(', ', array_fill(0, count($payloads), $placeholders));

        $bindings = [];
        foreach ($payloads as $row) {
            foreach ($columns as $col) {
                $bindings[] = $row[$col] ?? null;
            }
        }

        $sql = "INSERT INTO `hr_master_employee_staging` ({$colList}) VALUES {$valuesPlaceholders} ON DUPLICATE KEY UPDATE {$updateSql}";

        DB::statement($sql, $bindings);
    }
}
