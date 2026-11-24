<?php

namespace App\Mail\HR\Pk;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiPkDiskusiBaru extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $itemNo;
    protected $itemLabel;
    protected $pengajuNama;
    protected $pengajuRole;
    protected $pertanyaan;
    protected $recipientNama;
    protected $recipientRole;
    protected $recipientType;
    protected $tagPeserta;
    protected $tagAm;
    protected $tagManager;
    protected $tagPesertaEmail;
    protected $tagAmEmail;
    protected $tagManagerEmail;

    public function __construct(
        $periode,
        $pesertaNik,
        $pesertaNama,
        $itemNo,
        $itemLabel,
        $pengajuNama,
        $pengajuRole,
        $pertanyaan,
        $recipientNama,
        $recipientRole,
        $recipientType,
        $tagPeserta = '',
        $tagAm = '',
        $tagManager = '',
        $tagPesertaEmail = '',
        $tagAmEmail = '',
        $tagManagerEmail = ''
    ) {
        $this->periode = (string) $periode;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->itemNo = $itemNo;
        $this->itemLabel = (string) $itemLabel;
        $this->pengajuNama = (string) $pengajuNama;
        $this->pengajuRole = (string) $pengajuRole;
        $this->pertanyaan = (string) $pertanyaan;
        $this->recipientNama = (string) $recipientNama;
        $this->recipientRole = (string) $recipientRole;
        $this->recipientType = (string) $recipientType;
        $this->tagPeserta = (string) $tagPeserta;
        $this->tagAm = (string) $tagAm;
        $this->tagManager = (string) $tagManager;
        $this->tagPesertaEmail = trim((string) $tagPesertaEmail);
        $this->tagAmEmail = trim((string) $tagAmEmail);
        $this->tagManagerEmail = trim((string) $tagManagerEmail);
    }

    public function build()
    {
        $itemSuffix = ($this->itemNo !== null && $this->itemNo !== '') ? ' (Item #' . $this->itemNo . ')' : '';
        $subject = '[Diskusi PK] Pertanyaan Baru dari ' . $this->pengajuNama . ' — Periode ' . $this->periode . $itemSuffix;
        $isPeserta = $this->recipientType === 'peserta';

        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject($subject)
            ->view('mail.hr.pk.notifikasi-pk-diskusi-baru', [
                'periode' => $this->periode,
                'pesertaNik' => $this->pesertaNik,
                'pesertaNama' => $this->pesertaNama,
                'itemLabel' => $this->itemLabel,
                'pengajuNama' => $this->pengajuNama,
                'pengajuRole' => $this->pengajuRole,
                'pertanyaan' => $this->pertanyaan,
                'recipientNama' => $this->recipientNama,
                'recipientRole' => $this->recipientRole,
                'recipientType' => $this->recipientType,
                'isPeserta' => $isPeserta,
                'tagPeserta' => $this->tagPeserta,
                'tagAm' => $this->tagAm,
                'tagManager' => $this->tagManager,
                'tagPesertaEmail' => $this->tagPesertaEmail,
                'tagAmEmail' => $this->tagAmEmail,
                'tagManagerEmail' => $this->tagManagerEmail,
                'gmailSubject' => $subject,
                'link' => $isPeserta ? url('/hr/pk-assessment') . '#diskusi' : url('/hr/pk-assessment') . '?tab=leader&diskusi=open',
                'linkLabel' => $isPeserta ? 'Lihat Diskusi' : 'Jawab Diskusi',
            ]);
    }
}
