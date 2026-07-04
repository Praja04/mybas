<?php

namespace App\Jobs\HR;

use App\Models\HR\HrWorkingTimeAndOvertime;
use App\Models\HR\HrWorkingTimeAndOvertimeBatch;
use App\Models\HR\HrWorkingTimeAndOvertimeStaging;
use App\Support\HrEmployeeNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessHrWorkingTimeAndOvertimeImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $batchId;
    protected $filePath;
    protected $username;
    protected $typeKaryawan;
    protected $isMitraKerja;
    protected $chunkSize = 500;

    public function __construct($batchId, $filePath, $username, $typeKaryawan = null)
    {
        $this->batchId = $batchId;
        $this->filePath = $filePath;
        $this->username = $username;
        $this->typeKaryawan = $typeKaryawan;
        $this->isMitraKerja = ($typeKaryawan === 'mitra_kerja');
    }

    public function handle()
    {
        $batch = HrWorkingTimeAndOvertimeBatch::where('batch_id', $this->batchId)->first();
        if (! $batch) {
            return;
        }

        try {
            $batch->update(['status' => 'processing']);

            HrWorkingTimeAndOvertimeStaging::where('send_by_username', $this->username)
                ->where('status', 'created')
                ->delete();

            $rows = $this->readCsvRows($this->filePath);

            $batch->total_data = count($rows);
            $batch->save();

            if (empty($rows)) {
                $batch->update(['status' => 'completed']);
                $this->deleteUploadedFile();
                return;
            }

            $existingKeySet = $this->getExistingKeySet(
                array_column($rows, 'nik'),
                array_column($rows, 'tgl_in')
            );

            $created = 0;
            $updated = 0;
            $chunks = array_chunk($rows, $this->chunkSize);

            foreach ($chunks as $chunk) {
                $payloads = [];
                $now = now();
                foreach ($chunk as $row) {
                    $rowKey = $row['nik'] . '|' . $row['tgl_in'];
                    $isUpdate = isset($existingKeySet[$rowKey]);

                    $payloads[] = [
                        'nik'              => $row['nik'],
                        'nama'             => $row['nama'],
                        'dept'             => $row['dept'],
                        'section'          => $row['section'],
                        'tgl_in'           => $row['tgl_in'],
                        'jam_spkl'         => $row['jam_spkl'],
                        'jam_hovt'         => $row['jam_hovt'],
                        'no_spkl'          => $row['no_spkl'],
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

                $this->insertChunk($payloads);
                unset($payloads, $chunk);
            }

            $batch->update([
                'created_count' => $created,
                'updated_count' => $updated,
                'status'        => 'completed',
            ]);

            $this->deleteUploadedFile();
        } catch (\Throwable $e) {
            $batch->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
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

        // Mode mitra_kerja: template compact
        //   Row 1 = Judul
        //   Row 2 = Header: NIK | Nama | Company | Dept | Section | Tgl In | Jam SPKL | Jam HOVT
        //   Row 3+ = value
        // Mode default (BAS): template panjang
        //   Row 1 = Judul (skip)
        //   Row 2+ = value (header tidak standar, langsung baca)
        $startRow   = $this->isMitraKerja ? 3 : 2;
        $colTglIn   = $this->isMitraKerja ? 5 : 6;
        $colJamSpkl = $this->isMitraKerja ? 6 : 20;
        $colJamHovt = $this->isMitraKerja ? 7 : 21;
        $colNoSpkl  = $this->isMitraKerja ? null : 24;

        $rows = [];
        $rowNumber = 0;

        while (($data = fgetcsv($fh, 0, $delimiter)) !== false) {
            $rowNumber++;

            if ($rowNumber < $startRow) {
                continue;
            }

            $nik = isset($data[0]) ? trim((string) $data[0]) : '';
            if ($nik === '') {
                continue;
            }

            $jamSpkl = $this->decimalOrNull($data, $colJamSpkl);
            $jamHovt = $this->decimalOrNull($data, $colJamHovt);

            if (($jamSpkl === null || $jamSpkl <= 0) && ($jamHovt === null || $jamHovt <= 0)) {
                continue;
            }

            $dept    = $this->str($data, 3);
            $section = $this->str($data, 4);

            $rows[] = [
                'nik'      => $nik,
                'nama'     => $this->str($data, 1),
                'dept'     => HrEmployeeNormalizer::normalizeDepartmenAndSubDepartmen(
                    $dept, null, $section
                )[0],
                'section'  => $section,
                'tgl_in'   => $this->normalizeDate($data, $colTglIn),
                'jam_spkl' => $jamSpkl,
                'jam_hovt' => $jamHovt,
                'no_spkl'  => $colNoSpkl !== null ? $this->str($data, $colNoSpkl) : null,
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

    protected function decimalOrNull(array $row, $index)
    {
        if (! isset($row[$index])) {
            return null;
        }
        $raw = trim((string) $row[$index]);
        if ($raw === '') {
            return null;
        }
        $value = str_replace(',', '.', $raw);
        if (! is_numeric($value)) {
            return null;
        }
        return (float) $value;
    }

    protected function normalizeDate(array $row, $index): ?string
    {
        if (! isset($row[$index])) {
            return null;
        }
        $raw = trim((string) $row[$index]);
        if ($raw === '') {
            return null;
        }
        $ts = strtotime(str_replace('/', '-', $raw));
        if ($ts === false) {
            return null;
        }
        return date('Y-m-d', $ts);
    }

    protected function getExistingKeySet(array $niks, array $tglIns): array
    {
        if (empty($niks)) {
            return [];
        }

        $rows = HrWorkingTimeAndOvertime::whereIn('nik', $niks)
            ->get(['nik', 'tgl_in']);

        $set = [];
        foreach ($rows as $r) {
            $set[$r->nik . '|' . ($r->tgl_in ?? '')] = true;
        }
        return $set;
    }

    protected function insertChunk(array $payloads): void
    {
        DB::table('hr_workingtimeandovertime_staging')->insert($payloads);
    }
}
