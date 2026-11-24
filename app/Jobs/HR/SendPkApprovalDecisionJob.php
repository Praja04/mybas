<?php

namespace App\Jobs\HR;

use App\Mail\HR\Pk\NotifikasiKeputusanPk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPkApprovalDecisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $group;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $approverNama;
    protected $approverRole;
    protected $recipients;

    public function __construct(
        $periode,
        $group,
        $pesertaNik,
        $pesertaNama,
        $approverNama,
        $approverRole,
        array $recipients
    ) {
        $this->periode = (string) $periode;
        $this->group = (string) $group;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->approverNama = (string) $approverNama;
        $this->approverRole = (string) $approverRole;
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
                    Log::warning('PK approval decision email skipped (email not found)', [
                        'nik' => $nik,
                        'periode' => $this->periode,
                        'peserta' => $this->pesertaNik,
                    ]);
                    continue;
                }

                try {
                    Mail::mailer('smtp')
                        ->to($email, $target['nama'])
                        ->send(new NotifikasiKeputusanPk(
                            $this->periode,
                            $this->group,
                            $this->pesertaNik,
                            $this->pesertaNama,
                            $this->approverNama,
                            $this->approverRole,
                            $target['nama'],
                            $target['type'],
                            $target['must_approve']
                        ));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('PK approval decision email failed', [
                        'nik' => $nik,
                        'email' => $email,
                        'periode' => $this->periode,
                        'peserta' => $this->pesertaNik,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('PK approval decision emails processed', [
                'periode' => $this->periode,
                'group' => $this->group,
                'peserta' => $this->pesertaNik,
                'approver_role' => $this->approverRole,
                'total' => count($targets),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            Log::error('PK approval decision email job failed', [
                'periode' => $this->periode,
                'peserta' => $this->pesertaNik,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
