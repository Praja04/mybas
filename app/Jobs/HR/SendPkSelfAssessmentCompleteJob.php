<?php

namespace App\Jobs\HR;

use App\Mail\HR\Pk\NotifikasiPkSelfLengkap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPkSelfAssessmentCompleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $group;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $recipients;

    public function __construct($periode, $group, $pesertaNik, $pesertaNama, array $recipients)
    {
        $this->periode = (string) $periode;
        $this->group = (string) $group;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
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
                    'role' => ($recipient['role'] ?? '') === 'Associate Manager' ? 'Associate Manager' : 'Manager',
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
                    Log::warning('PK self assessment complete email skipped (email not found)', [
                        'nik' => $nik,
                        'periode' => $this->periode,
                        'peserta' => $this->pesertaNik,
                    ]);
                    continue;
                }

                try {
                    Mail::mailer('smtp')
                        ->to($email, $target['nama'])
                        ->send(new NotifikasiPkSelfLengkap(
                            $this->periode,
                            $this->group,
                            $this->pesertaNik,
                            $this->pesertaNama,
                            $target['nama'],
                            $target['role']
                        ));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('PK self assessment complete email failed', [
                        'nik' => $nik,
                        'email' => $email,
                        'periode' => $this->periode,
                        'peserta' => $this->pesertaNik,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('PK self assessment complete emails processed', [
                'periode' => $this->periode,
                'group' => $this->group,
                'peserta' => $this->pesertaNik,
                'total' => count($targets),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            Log::error('PK self assessment complete email job failed', [
                'periode' => $this->periode,
                'peserta' => $this->pesertaNik,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
