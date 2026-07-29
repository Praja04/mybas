<?php

namespace App\Jobs\HR;

use App\Models\HR\HrIzin;
use App\Models\HR\HrIzinBatch;
use App\Models\HR\HrIzinStaging;
use App\Models\HR\HrWorkingTimeAndOvertime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessHrIzinConfirm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 900;

    protected $batchId;
    protected $stagingIds;
    protected $decisions;
    protected $username;

    public function __construct($batchId, array $stagingIds, $username, array $decisions = [])
    {
        $this->batchId    = $batchId;
        $this->stagingIds = $stagingIds;
        $this->username   = $username;
        $this->decisions  = $decisions;
    }

    public function handle()
    {
        $batch = HrIzinBatch::where('batch_id', $this->batchId)->first();
        if (! $batch) {
            return;
        }

        try {
            $batch->update([
                'confirm_status'    => 'processing',
                'confirm_total'     => count($this->stagingIds),
                'confirm_processed' => 0,
                'confirm_error'     => null,
                'overlap_log'       => null,
            ]);

            $staging = HrIzinStaging::where('batch_id', $this->batchId)
                ->whereIn('id', $this->stagingIds)
                ->get();

            if ($staging->isEmpty()) {
                $batch->update(['confirm_status' => 'done']);
                return;
            }

            $processed = 0;
            $deletedOvertime = 0;
            $deletedMangkir  = 0;
            $overlapLog = [];
            $chunks = $staging->chunk(200);

            DB::transaction(function () use ($chunks, &$processed, &$deletedOvertime, &$deletedMangkir, &$overlapLog, $batch) {
                foreach ($chunks as $chunk) {
                    $insertPayloads = [];
                    $updateRows     = [];
                    $now = now();

                    foreach ($chunk as $row) {
                        $stagingId = (int) $row->id;
                        $decision  = $this->decisions[$stagingId] ?? 'keep_izin';

                        $decision = in_array($decision, ['keep_izin', 'keep_lembur', 'skip'], true)
                            ? $decision
                            : 'keep_izin';

                        $row->decision = $decision;

                        if ($decision === 'skip') {
                            continue;
                        }

                        if ($row->status === 'updated') {
                            $updateRows[] = $row;
                        } else {
                            $insertPayloads[] = [
                                'nik'              => $row->nik,
                                'nama'             => $row->nama,
                                'dept'             => $row->dept,
                                'section'          => $row->section,
                                'tgl'              => $row->tgl,
                                'no_spi'           => $row->no_spi,
                                'kode_ijin'        => $row->kode_ijin,
                                'keterangan'       => $row->keterangan,
                                'send_by_username' => $this->username,
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ];
                        }
                    }

                    foreach ($chunk as $row) {
                        $decision = $row->decision ?? 'keep_izin';
                        if ($decision === 'skip') continue;
                        if (strtoupper(trim((string) $row->kode_ijin)) === 'A') continue;
                        if (empty($row->tgl) || empty($row->nik)) continue;

                        $deleted = HrIzin::where('nik', $row->nik)
                            ->where('tgl', $row->tgl)
                            ->where('kode_ijin', 'A')
                            ->delete();

                        if ($deleted > 0) {
                            $deletedMangkir += $deleted;
                            $overlapLog[] = sprintf(
                                '[MANGKIR] %s | %s | tgl=%s | mangkir dihapus: %d (izin prioritas)',
                                $row->nik,
                                $row->nama,
                                $row->tgl,
                                $deleted
                            );
                        }
                    }

                    if (! empty($insertPayloads)) {
                        HrIzin::insert($insertPayloads);
                    }

                    foreach ($updateRows as $uRow) {
                        HrIzin::where('nik', $uRow->nik)
                            ->where('tgl', $uRow->tgl)
                            ->where('no_spi', $uRow->no_spi)
                            ->update([
                                'nama'             => $uRow->nama,
                                'dept'             => $uRow->dept,
                                'section'          => $uRow->section,
                                'kode_ijin'        => $uRow->kode_ijin,
                                'keterangan'       => $uRow->keterangan,
                                'send_by_username' => $this->username,
                                'updated_at'       => $now,
                            ]);
                    }

                    foreach ($chunk as $row) {
                        $decision = $row->decision ?? 'keep_izin';
                        unset($row->decision);

                        if ($decision !== 'keep_izin') {
                            continue;
                        }

                        if (empty($row->tgl) || empty($row->nik)) {
                            continue;
                        }

                        $deleted = HrWorkingTimeAndOvertime::where('nik', $row->nik)
                            ->where('tgl_in', $row->tgl)
                            ->delete();

                        if ($deleted > 0) {
                            $deletedOvertime += $deleted;
                            $overlapLog[] = sprintf(
                                '%s | %s | %s | tgl=%s | lembur dihapus: %d',
                                $row->nik,
                                $row->nama,
                                $row->no_spi,
                                $row->tgl,
                                $deleted
                            );
                        }
                    }

                    $chunkIds = $chunk->pluck('id')->all();
                    HrIzinStaging::whereIn('id', $chunkIds)->delete();

                    $processed += count($chunkIds);

                    $batch->update([
                        'confirm_processed' => $processed,
                        'confirmed_count'   => (int) $batch->confirmed_count + count($chunkIds),
                    ]);
                }
            });

            $batch->update([
                'confirm_status'           => 'done',
                'deleted_overtime_count'   => $deletedOvertime,
                'overlap_count'            => count($overlapLog),
                'overlap_log'              => empty($overlapLog) ? null : implode("\n", $overlapLog),
            ]);
        } catch (\Throwable $e) {
            $batch->update([
                'confirm_status' => 'failed',
                'confirm_error'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
