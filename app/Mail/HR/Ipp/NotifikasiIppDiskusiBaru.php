<?php

namespace App\Mail\HR\Ipp;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiIppDiskusiBaru extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $tahapanLabel;
    protected $kpiNo;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $pertanyaan;
    protected $recipientNama;
    protected $recipientRole;
    protected $tagPeserta;
    protected $tagAm;
    protected $tagManager;
    protected $tagPesertaEmail;
    protected $tagAmEmail;
    protected $tagManagerEmail;

    public function __construct(
        $periode,
        $tahapanLabel,
        $kpiNo,
        $pesertaNik,
        $pesertaNama,
        $pertanyaan,
        $recipientNama,
        $recipientRole,
        $tagPeserta,
        $tagAm,
        $tagManager,
        $tagPesertaEmail = '',
        $tagAmEmail = '',
        $tagManagerEmail = ''
    ) {
        $this->periode = (string) $periode;
        $this->tahapanLabel = (string) $tahapanLabel;
        $this->kpiNo = $kpiNo;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->pertanyaan = (string) $pertanyaan;
        $this->recipientNama = (string) $recipientNama;
        $this->recipientRole = (string) $recipientRole;
        $this->tagPeserta = (string) $tagPeserta;
        $this->tagAm = (string) $tagAm;
        $this->tagManager = (string) $tagManager;
        $this->tagPesertaEmail = trim((string) $tagPesertaEmail);
        $this->tagAmEmail = trim((string) $tagAmEmail);
        $this->tagManagerEmail = trim((string) $tagManagerEmail);
    }

    public function build()
    {
        $kpiSuffix = ($this->kpiNo !== null && $this->kpiNo !== '') ? ' (KPI #' . $this->kpiNo . ')' : '';
        $subject = '[Diskusi IPP] Pertanyaan Baru dari ' . $this->pesertaNama . ' — ' . $this->tahapanLabel . $kpiSuffix;

        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject($subject)
            ->view('mail.hr.ipp.notifikasi-ipp-diskusi-baru', [
                'periode' => $this->periode,
                'tahapanLabel' => $this->tahapanLabel,
                'kpiNo' => $this->kpiNo,
                'kpiText' => ($this->kpiNo !== null && $this->kpiNo !== '') ? ', KPI #' . $this->kpiNo : '',
                'pesertaNik' => $this->pesertaNik,
                'pesertaNama' => $this->pesertaNama,
                'pertanyaan' => $this->pertanyaan,
                'recipientNama' => $this->recipientNama,
                'recipientRole' => $this->recipientRole,
                'tagPeserta' => $this->tagPeserta,
                'tagAm' => $this->tagAm,
                'tagManager' => $this->tagManager,
                'tagPesertaEmail' => $this->tagPesertaEmail,
                'tagAmEmail' => $this->tagAmEmail,
                'tagManagerEmail' => $this->tagManagerEmail,
                'gmailSubject' => $subject,
                'link' => url('/hr/ipp-user-approval'),
            ]);
    }
}
