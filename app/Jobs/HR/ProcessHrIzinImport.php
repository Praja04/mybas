<?php

namespace App\Jobs\HR;

use App\Models\HR\HrIzin;
use App\Models\HR\HrIzinBatch;
use App\Models\HR\HrIzinStaging;
use App\Support\HrEmployeeNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessHrIzinImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $batchId;
    protected $filePath;
    protected $username;
    protected $type;
    protected $chunkSize = 500;

    public function __construct($batchId, $filePath, $username, $type = 'izin')
    {
        $this->batchId = $batchId;
        $this->filePath = $filePath;
        $this->username = $username;
        $this->type = $type;
    }

    public function handle()
    {
        $batch = HrIzinBatch::where('batch_id', $this->batchId)->first();
        if (! $batch) {
            $this->deleteUploadedFile();
            return;
        }

        try {
            $batch->update(['status' => 'processing']);

            HrIzinStaging::where('send_by_username', $this->username)
                ->where('status', 'created')
                ->delete();

            $rows = $this->type === 'mangkir'
                ? $this->readMangkirRows($this->filePath)
                : $this->readIzinRows($this->filePath);

            $batch->total_data = count($rows);
            $batch->save();

            if (empty($rows)) {
                $batch->update(['status' => 'completed']);
                $this->deleteUploadedFile();
                return;
            }

            $existingKeySet = $this->getExistingKeySet(
                array_column($rows, 'nik')
            );

            $created = 0;
            $updated = 0;
            $chunks = array_chunk($rows, $this->chunkSize);

            foreach ($chunks as $chunk) {
                $payloads = [];
                $now = now();
                foreach ($chunk as $row) {
                    $rowKey = $row['nik'] . '|' . $row['tgl'] . '|' . $row['no_spi'];
                    $isUpdate = isset($existingKeySet[$rowKey]);

                    $payloads[] = [
                        'nik'              => $row['nik'],
                        'nama'             => $row['nama'],
                        'dept'             => $row['dept'],
                        'section'          => $row['section'],
                        'tgl'              => $row['tgl'],
                        'no_spi'           => $row['no_spi'],
                        'kode_ijin'        => $row['kode_ijin'],
                        'keterangan'       => $row['keterangan'],
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
            throw $e;
        }
    }

    protected function deleteUploadedFile(): void
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    protected function readIzinRows(string $path): array
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

        $allowedKodeIjin = [
            'KD', 'CB', 'CDC1', 'CDC2', 'CDC3', 'CHD', 'CIM',
            'CK', 'CKT', 'CH', 'CM', 'CNA', 'CHJ', 'C2', 'C',
            'CUT', 'IM', 'S', 'SKK',
            'A',
        ];

        while (($data = fgetcsv($fh, 0, $delimiter)) !== false) {
            $rowNumber++;

            if ($rowNumber < 2) {
                continue;
            }

            $nik = isset($data[0]) ? trim((string) $data[0]) : '';
            if ($nik === '') {
                continue;
            }

            $kodeIjin = strtoupper(trim((string) ($data[8] ?? '')));
            if (!in_array($kodeIjin, $allowedKodeIjin, true)) {
                continue;
            }

            $noSpi = $this->str($data, 7);
            if ($noSpi === null) {
                continue;
            }

            $dateRaw = isset($data[6]) ? trim((string) $data[6]) : '';
            $tgl = $dateRaw === '' ? null : HrEmployeeNormalizer::normalizeDate($dateRaw);
            if ($tgl === null) {
                continue;
            }

            $deptRaw = $this->str($data, 3);
            $section = $this->str($data, 4);

            $rows[] = [
                'nik'        => $nik,
                'nama'       => $this->str($data, 1),
                'dept'       => HrEmployeeNormalizer::normalizeDepartmenAndSubDepartmen(
                    $deptRaw, null, $section
                )[0],
                'section'    => $section,
                'tgl'        => $tgl,
                'no_spi'     => $noSpi,
                'kode_ijin'  => $kodeIjin,
                'keterangan' => $this->str($data, 9),
            ];
        }

        fclose($fh);
        return $rows;
    }

    protected function readMangkirRows(string $path): array
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

            if ($rowNumber < 2) {
                continue;
            }

            $nik = isset($data[0]) ? trim((string) $data[0]) : '';
            if ($nik === '') {
                continue;
            }

            $dateRaw = isset($data[6]) ? trim((string) $data[6]) : '';
            $tgl = $dateRaw === '' ? null : HrEmployeeNormalizer::normalizeDate($dateRaw);
            if ($tgl === null) {
                continue;
            }

            $deptRaw = $this->str($data, 3);
            $section = $this->str($data, 4);

            $rows[] = [
                'nik'        => $nik,
                'nama'       => $this->str($data, 1),
                'dept'       => HrEmployeeNormalizer::normalizeDepartmenAndSubDepartmen(
                    $deptRaw, null, $section
                )[0],
                'section'    => $section,
                'tgl'        => $tgl,
                'no_spi'     => null,
                'kode_ijin'  => 'A',
                'keterangan' => null,
            ];
        }

        fclose($fh);

        if (!empty($rows)) {
            $niks = array_unique(array_column($rows, 'nik'));
            $existingLevels = DB::table('hr_master_employee')
                ->whereIn('NIK', $niks)
                ->pluck('Level', 'NIK')
                ->all();

            $excludedNiks = [];
            foreach ($niks as $nik) {
                if (!isset($existingLevels[$nik])) {
                    $excludedNiks[$nik] = true;
                    continue;
                }
                $level = (int) $existingLevels[$nik];
                if ($level >= 8 && $level <= 13) {
                    $excludedNiks[$nik] = true;
                }
            }

            if (!empty($excludedNiks)) {
                $rows = array_values(array_filter($rows, function ($row) use ($excludedNiks) {
                    return !isset($excludedNiks[$row['nik']]);
                }));
            }
        }

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

    protected function getExistingKeySet(array $niks): array
    {
        if (empty($niks)) {
            return [];
        }

        $rows = HrIzin::whereIn('nik', $niks)
            ->get(['nik', 'tgl', 'no_spi']);

        $set = [];
        foreach ($rows as $r) {
            $set[$r->nik . '|' . ($r->tgl ?? '') . '|' . ($r->no_spi ?? '')] = true;
        }
        return $set;
    }

    protected function insertChunk(array $payloads): void
    {
        DB::table('hr_izin_staging')->insert($payloads);
    }
}
