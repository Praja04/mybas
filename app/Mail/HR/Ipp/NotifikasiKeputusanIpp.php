<?php

namespace App\Mail\HR\Ipp;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiKeputusanIpp extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $tahapanLabel;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $approverNama;
    protected $approverRole;
    protected $action;
    protected $note;
    protected $recipientNama;
    protected $recipientType;
    protected $mustApprove;

    public function __construct(
        $periode,
        $tahapanLabel,
        $pesertaNik,
        $pesertaNama,
        $approverNama,
        $approverRole,
        $action,
        $note,
        $recipientNama,
        $recipientType,
        $mustApprove = false
    ) {
        $this->periode = $periode;
        $this->tahapanLabel = $tahapanLabel;
        $this->pesertaNik = $pesertaNik;
        $this->pesertaNama = $pesertaNama;
        $this->approverNama = $approverNama;
        $this->approverRole = $approverRole;
        $this->action = $action;
        $this->note = $note;
        $this->recipientNama = $recipientNama;
        $this->recipientType = $recipientType;
        $this->mustApprove = (bool) $mustApprove;
    }

    public function build()
    {
        $statusLabel = $this->action === 'approved' ? 'Disetujui' : 'Ditolak';
        $isPeserta = $this->recipientType === 'peserta';
        $mustApprove = $this->mustApprove && !$isPeserta && $this->action === 'approved';

        $subject = $mustApprove
            ? 'IPP Periode ' . $this->periode . ' (' . $this->tahapanLabel . ') — Menunggu Approval Anda (Manager)'
            : 'IPP Periode ' . $this->periode . ' (' . $this->tahapanLabel . ') — ' . $statusLabel . ' oleh ' . $this->approverRole;

        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject($subject)
            ->view('mail.hr.ipp.notifikasi-keputusan-ipp', [
                'periode' => $this->periode,
                'tahapanLabel' => $this->tahapanLabel,
                'pesertaNik' => $this->pesertaNik,
                'pesertaNama' => $this->pesertaNama,
                'approverNama' => $this->approverNama,
                'approverRole' => $this->approverRole,
                'action' => $this->action,
                'statusLabel' => $statusLabel,
                'note' => $this->note,
                'recipientNama' => $this->recipientNama,
                'recipientType' => $this->recipientType,
                'mustApprove' => $mustApprove,
                'link' => $isPeserta ? url('/hr/ipp-aktual') : url('/hr/ipp-user-approval'),
                'linkLabel' => $mustApprove ? 'Approve Sekarang' : ($isPeserta ? 'Lihat Aktual IPP' : 'Buka Approval IPP'),
            ]);
    }
}
