<?php

namespace App\Jobs\HR;

use App\Mail\HR\Pk\NotifikasiPkDiskusiBaru;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPkDiskusiNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $itemNo;
    protected $itemLabel;
    protected $pengajuNama;
    protected $pengajuRole;
    protected $pertanyaan;
    protected $tagPeserta;
    protected $tagAm;
    protected $tagManager;
    protected $tagPesertaNik;
    protected $tagAmNik;
    protected $tagManagerNik;
    protected $recipients;

    public function __construct(
        $periode,
        $pesertaNik,
        $pesertaNama,
        $itemNo,
        $itemLabel,
        $pengajuNama,
        $pengajuRole,
        $pertanyaan,
        $tagPeserta,
        $tagAm,
        $tagManager,
        $tagPesertaNik,
        $tagAmNik,
        $tagManagerNik,
        array $recipients
    ) {
        $this->periode = (string) $periode;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->itemNo = $itemNo;
        $this->itemLabel = (string) $itemLabel;
        $this->pengajuNama = (string) $pengajuNama;
        $this->pengajuRole = (string) $pengajuRole;
        $this->pertanyaan = (string) $pertanyaan;
        $this->tagPeserta = (string) $tagPeserta;
        $this->tagAm = (string) $tagAm;
        $this->tagManager = (string) $tagManager;
        $this->tagPesertaNik = strtoupper(trim((string) $tagPesertaNik));
        $this->tagAmNik = strtoupper(trim((string) $tagAmNik));
        $this->tagManagerNik = strtoupper(trim((string) $tagManagerNik));
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
                    'role' => trim((string) ($recipient['role'] ?? 'Atasan')),
                    'type' => ($recipient['type'] ?? 'atasan') === 'peserta' ? 'peserta' : 'atasan',
                ];
            }

            if (empty($targets)) {
                return;
            }

            $lookupNiks = array_keys($targets);
            foreach ([$this->tagPesertaNik, $this->tagAmNik, $this->tagManagerNik] as $tagNik) {
                if ($tagNik !== '' && !in_array($tagNik, $lookupNiks, true)) {
                    $lookupNiks[] = $tagNik;
                }
            }

            $emails = \App\Support\PmsEmailResolver::mapByNik($lookupNiks);

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
                    Log::warning('PK diskusi notification skipped (email not found)', [
                        'nik' => $nik,
                        'periode' => $this->periode,
                        'peserta' => $this->pesertaNik,
                    ]);
                    continue;
                }

                try {
                    Mail::mailer('smtp')
                        ->to($email, $target['nama'])
                        ->send(new NotifikasiPkDiskusiBaru(
                            $this->periode,
                            $this->pesertaNik,
                            $this->pesertaNama,
                            $this->itemNo,
                            $this->itemLabel,
                            $this->pengajuNama,
                            $this->pengajuRole,
                            $this->pertanyaan,
                            $target['nama'],
                            $target['role'],
                            $target['type'],
                            $this->tagPeserta,
                            $this->tagAm,
                            $this->tagManager,
                            (string) ($emails[$this->tagPesertaNik] ?? ''),
                            (string) ($emails[$this->tagAmNik] ?? ''),
                            (string) ($emails[$this->tagManagerNik] ?? '')
                        ));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('PK diskusi notification email failed', [
                        'nik' => $nik,
                        'email' => $email,
                        'periode' => $this->periode,
                        'peserta' => $this->pesertaNik,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('PK diskusi notification emails processed', [
                'periode' => $this->periode,
                'item_no' => $this->itemNo,
                'peserta' => $this->pesertaNik,
                'pengaju' => $this->pengajuNama,
                'total' => count($targets),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            Log::error('PK diskusi notification job failed', [
                'periode' => $this->periode,
                'peserta' => $this->pesertaNik,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
