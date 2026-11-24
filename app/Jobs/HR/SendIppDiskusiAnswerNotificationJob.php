<?php

namespace App\Jobs\HR;

use App\Mail\HR\Ipp\NotifikasiIppDiskusiDijawab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendIppDiskusiAnswerNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 900;

    protected $periode;
    protected $tahapan;
    protected $tahapanLabel;
    protected $kpiNo;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $pertanyaan;
    protected $jawaban;
    protected $penjawabNik;
    protected $penjawabNama;
    protected $penjawabRole;
    protected $tagPeserta;
    protected $tagAm;
    protected $tagManager;
    protected $tagPesertaNik;
    protected $tagAmNik;
    protected $tagManagerNik;
    protected $recipients;

    public function __construct(
        $periode,
        $tahapan,
        $tahapanLabel,
        $kpiNo,
        $pesertaNik,
        $pesertaNama,
        $pertanyaan,
        $jawaban,
        $penjawabNik,
        $penjawabNama,
        $penjawabRole,
        $tagPeserta,
        $tagAm,
        $tagManager,
        $tagPesertaNik,
        $tagAmNik,
        $tagManagerNik,
        array $recipients
    ) {
        $this->periode = (string) $periode;
        $this->tahapan = (string) $tahapan;
        $this->tahapanLabel = (string) $tahapanLabel;
        $this->kpiNo = $kpiNo;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->pertanyaan = (string) $pertanyaan;
        $this->jawaban = (string) $jawaban;
        $this->penjawabNik = strtoupper(trim((string) $penjawabNik));
        $this->penjawabNama = (string) $penjawabNama;
        $this->penjawabRole = (string) $penjawabRole;
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
                    'type' => ($recipient['type'] ?? 'peserta') === 'atasan' ? 'atasan' : 'peserta',
                    'role' => trim((string) ($recipient['role'] ?? 'Peserta')),
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

            $fallbackEmails = DB::table('users')
                ->whereIn('nik', $lookupNiks)
                ->pluck('email', 'nik');

            foreach ($lookupNiks as $lookupNik) {
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
                try {
                    $email = trim((string) ($emails[$nik] ?? ''));
                    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        $skipped++;
                        Log::warning('IPP diskusi answer notification skipped (email not found)', [
                            'nik' => $nik,
                            'periode' => $this->periode,
                            'tahapan' => $this->tahapan,
                            'peserta' => $this->pesertaNik,
                        ]);
                        continue;
                    }

                    Mail::mailer('smtp')
                        ->to($email, $target['nama'])
                        ->send(new NotifikasiIppDiskusiDijawab(
                            $this->periode,
                            $this->tahapanLabel,
                            $this->kpiNo,
                            $this->pesertaNik,
                            $this->pesertaNama,
                            $this->pertanyaan,
                            $this->jawaban,
                            $this->penjawabNama,
                            $this->penjawabRole,
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
                    Log::warning('IPP diskusi answer notification email failed', [
                        'nik' => $nik,
                        'periode' => $this->periode,
                        'tahapan' => $this->tahapan,
                        'peserta' => $this->pesertaNik,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('IPP diskusi answer notification emails processed', [
                'periode' => $this->periode,
                'tahapan' => $this->tahapan,
                'kpi_no' => $this->kpiNo,
                'peserta' => $this->pesertaNik,
                'penjawab' => $this->penjawabNik,
                'penjawab_role' => $this->penjawabRole,
                'total' => count($targets),
                'sent' => $sent,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            Log::error('IPP diskusi answer notification job failed', [
                'periode' => $this->periode,
                'tahapan' => $this->tahapan,
                'peserta' => $this->pesertaNik,
                'penjawab' => $this->penjawabNik,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
