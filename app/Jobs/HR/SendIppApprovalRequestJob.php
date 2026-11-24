<?php

namespace App\Jobs\HR;

use App\Mail\HR\Ipp\NotifikasiIppSiapApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendIppApprovalRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $tahapan;
    protected $tahapanLabel;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $recipientNik;
    protected $recipientNama;
    protected $recipientRole;

    public function __construct(
        $periode,
        $tahapan,
        $tahapanLabel,
        $pesertaNik,
        $pesertaNama,
        $recipientNik,
        $recipientNama,
        $recipientRole
    ) {
        $this->periode = (string) $periode;
        $this->tahapan = (string) $tahapan;
        $this->tahapanLabel = (string) $tahapanLabel;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->recipientNik = (string) $recipientNik;
        $this->recipientNama = (string) $recipientNama;
        $this->recipientRole = (string) $recipientRole;
    }

    public function handle()
    {
        try {
            $nik = strtoupper(trim($this->recipientNik));
            if ($nik === '') {
                return;
            }

            $email = (string) (\App\Support\PmsEmailResolver::forNik($nik) ?? '');

            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                Log::warning('IPP approval request email skipped (email not found)', [
                    'nik' => $nik,
                    'periode' => $this->periode,
                    'tahapan' => $this->tahapan,
                ]);
                return;
            }

            Mail::mailer('smtp')
                ->to($email, $this->recipientNama)
                ->send(new NotifikasiIppSiapApproval(
                    $this->periode,
                    $this->tahapanLabel,
                    $this->pesertaNik,
                    $this->pesertaNama,
                    $this->recipientNama,
                    $this->recipientRole
                ));

            Log::info('IPP approval request email sent', [
                'periode' => $this->periode,
                'tahapan' => $this->tahapan,
                'peserta' => $this->pesertaNik,
                'to' => $nik,
                'role' => $this->recipientRole,
            ]);
        } catch (\Throwable $e) {
            Log::error('IPP approval request email job failed', [
                'periode' => $this->periode,
                'tahapan' => $this->tahapan,
                'to' => $this->recipientNik,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
