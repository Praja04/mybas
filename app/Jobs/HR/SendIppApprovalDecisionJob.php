<?php

namespace App\Jobs\HR;

use App\Mail\HR\Ipp\NotifikasiKeputusanIpp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendIppApprovalDecisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $tahapanLabel;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $approverNama;
    protected $approverRole;
    protected $action;
    protected $note;
    protected $recipients;

    public function __construct(
        $periode,
        $tahapanLabel,
        $pesertaNik,
        $pesertaNama,
        $approverNama,
        $approverRole,
        $action,
        $note,
        array $recipients
    ) {
        $this->periode = (string) $periode;
        $this->tahapanLabel = (string) $tahapanLabel;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->approverNama = (string) $approverNama;
        $this->approverRole = (string) $approverRole;
        $this->action = (string) $action;
        $this->note = (string) $note;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        try {
            $targets = [];
            foreach ($this->recipients as $recipient) {
                $nik = strtoupper(trim((string) ($recipient['nik'] ?? '')));
                if ($nik === '' || isset($targets[$nik])) {
                    continue;
                }

                $targets[$nik] = [
                    'nik' => $nik,
                    'nama' => trim((string) ($recipient['nama'] ?? '')),
                    'type' => ($recipient['type'] ?? 'peserta') === 'atasan' ? 'atasan' : 'peserta',
                    'must_approve' => !empty($recipient['must_approve']),
                ];
            }

            if (empty($targets)) {
                return;
            }

            $emails = \App\Support\PmsEmailResolver::mapByNik(array_keys($targets));

            $sent = 0;
            $failed = 0;
            $skipped = 0;

            foreach ($targets as $nik => $target) {
                $email = trim((string) ($emails[$nik] ?? ''));
                if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $email = trim((string) DB::table('users')->where('nik', $nik)->value('email'));
                }

                if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $skipped++;
                    Log::warning('IPP approval decision email skipped (email not found)', [
                        'nik' => $nik,
                        'periode' => $this->periode,
                    ]);
                    continue;
                }

                try {
                    Mail::mailer('smtp')
                        ->to($email, $target['nama'])
                        ->send(new NotifikasiKeputusanIpp(
                            $this->periode,
                            $this->tahapanLabel,
                            $this->pesertaNik,
                            $this->pesertaNama,
                            $this->approverNama,
                            $this->approverRole,
                            $this->action,
                            $this->note,
                            $target['nama'],
                            $target['type'],
                            $target['must_approve']
                        ));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('IPP approval decision email failed', [
                        'nik' => $nik,
                        'email' => $email,
                        'periode' => $this->periode,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('IPP approval decision emails processed', [
                'periode' => $this->periode,
                'action' => $this->action,
                'total' => count($targets),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            Log::error('IPP approval decision email job failed', [
                'periode' => $this->periode,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
