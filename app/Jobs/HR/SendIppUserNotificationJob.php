<?php

namespace App\Jobs\HR;

use App\Mail\HR\Ipp\NotifikasiIppBaru;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendIppUserNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $recipients;
    protected $pembuat;

    public function __construct($periode, array $recipients, $pembuat = null)
    {
        $this->periode = (string) $periode;
        $this->recipients = $recipients;
        $this->pembuat = $pembuat;
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
                    'email' => null,
                ];
            }

            if (empty($targets)) {
                return;
            }

            $emails = \App\Support\PmsEmailResolver::mapByNik(array_keys($targets));

            $fallbackEmails = DB::table('users')
                ->whereIn('nik', array_keys($targets))
                ->pluck('email', 'nik');

            foreach (array_keys($targets) as $lookupNik) {
                $email = trim((string) ($emails[$lookupNik] ?? ''));
                if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    continue;
                }
                $fallback = trim((string) ($fallbackEmails[$lookupNik] ?? ''));
                if ($fallback !== '' && filter_var($fallback, FILTER_VALIDATE_EMAIL) !== false) {
                    $emails[$lookupNik] = $fallback;
                }
            }

            $sent = 0;
            $failed = 0;
            $skipped = 0;

            foreach ($targets as $nik => $target) {
                $email = trim((string) ($emails[$nik] ?? ''));
                if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $skipped++;
                    Log::warning('IPP notification email skipped (email not found)', [
                        'nik' => $nik,
                        'periode' => $this->periode,
                    ]);
                    continue;
                }

                try {
                    Mail::mailer('smtp')
                        ->to($email, $target['nama'])
                        ->send(new NotifikasiIppBaru($this->periode, $target['nama'], $this->pembuat));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('IPP notification email failed', [
                        'nik' => $nik,
                        'email' => $email,
                        'periode' => $this->periode,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('IPP notification emails processed', [
                'periode' => $this->periode,
                'total' => count($targets),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            Log::error('IPP notification email job failed', [
                'periode' => $this->periode,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
