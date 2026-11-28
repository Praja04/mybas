<?php

namespace App\Jobs\HR;

use App\Models\HR\HrWorkingTimeAndOvertime;
use App\Models\HR\HrWorkingTimeAndOvertimeBatch;
use App\Models\HR\HrWorkingTimeAndOvertimeStaging;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessHrWorkingTimeAndOvertimeConfirm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 900;

    protected $batchId;
    protected $stagingIds;
    protected $username;

    public function __construct($batchId, array $stagingIds, $username)
    {
        $this->batchId = $batchId;
        $this->stagingIds = $stagingIds;
        $this->username = $username;
    }

    public function handle()
    {
        $batch = HrWorkingTimeAndOvertimeBatch::where('batch_id', $this->batchId)->first();
        if (! $batch) {
            return;
        }

        try {
            $batch->update([
                'confirm_status'    => 'processing',
                'confirm_total'     => count($this->stagingIds),
                'confirm_processed' => 0,
                'confirm_error'     => null,
            ]);

            $staging = HrWorkingTimeAndOvertimeStaging::where('batch_id', $this->batchId)
                ->whereIn('id', $this->stagingIds)
                ->get();

            if ($staging->isEmpty()) {
                $batch->update(['confirm_status' => 'done']);
                return;
            }

            $processed = 0;
            $chunks = $staging->chunk(200);

            DB::transaction(function () use ($chunks, &$processed, $batch) {
                foreach ($chunks as $chunk) {
                    $insertPayloads = [];
                    $updateRows     = [];
                    $now = now();

                    foreach ($chunk as $row) {
                        if ($row->status === 'updated') {
                            $updateRows[] = $row;
                        } else {
                            $insertPayloads[] = [
                                'nik'              => $row->nik,
                                'nama'             => $row->nama,
                                'dept'             => $row->dept,
                                'section'          => $row->section,
                                'tgl_in'           => $row->tgl_in,
                                'jam_spkl'         => $row->jam_spkl,
                                'jam_hovt'         => $row->jam_hovt,
                                'no_spkl'          => $row->no_spkl,
                                'send_by_username' => $this->username,
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ];
                        }
                    }

                    if (! empty($insertPayloads)) {
                        HrWorkingTimeAndOvertime::insert($insertPayloads);
                    }

                    foreach ($updateRows as $uRow) {
                        HrWorkingTimeAndOvertime::where('nik', $uRow->nik)
                            ->where('tgl_in', $uRow->tgl_in)
                            ->update([
                                'jam_spkl'         => $uRow->jam_spkl,
                                'jam_hovt'         => $uRow->jam_hovt,
                                'no_spkl'          => $uRow->no_spkl,
                                'send_by_username' => $this->username,
                                'updated_at'       => $now,
                            ]);
                    }

                    $chunkIds = $chunk->pluck('id')->all();
                    HrWorkingTimeAndOvertimeStaging::whereIn('id', $chunkIds)->delete();

                    $processed += count($chunkIds);

                    $batch->update([
                        'confirm_processed' => $processed,
                        'confirmed_count'   => (int) $batch->confirmed_count + count($chunkIds),
                    ]);
                }
            });

            $batch->update(['confirm_status' => 'done']);
        } catch (\Throwable $e) {
            $batch->update([
                'confirm_status' => 'failed',
                'confirm_error'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
