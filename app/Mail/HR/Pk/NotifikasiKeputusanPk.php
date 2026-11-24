<?php

namespace App\Mail\HR\Pk;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiKeputusanPk extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $group;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $approverNama;
    protected $approverRole;
    protected $recipientNama;
    protected $recipientType;
    protected $mustApprove;

    public function __construct(
        $periode,
        $group,
        $pesertaNik,
        $pesertaNama,
        $approverNama,
        $approverRole,
        $recipientNama,
        $recipientType,
        $mustApprove = false
    ) {
        $this->periode = (string) $periode;
        $this->group = (string) $group;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->approverNama = (string) $approverNama;
        $this->approverRole = (string) $approverRole;
        $this->recipientNama = (string) $recipientNama;
        $this->recipientType = (string) $recipientType;
        $this->mustApprove = (bool) $mustApprove;
    }

    public function build()
    {
        $isPeserta = $this->recipientType === 'peserta';
        $mustApprove = $this->mustApprove && !$isPeserta;

        $subject = $mustApprove
            ? 'Penilaian Karya Periode ' . $this->periode . ' (Group ' . $this->group . ') — Menunggu Approval Anda (Manager)'
            : 'Penilaian Karya Periode ' . $this->periode . ' (Group ' . $this->group . ') — Disetujui oleh ' . $this->approverRole;

        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject($subject)
            ->view('mail.hr.pk.notifikasi-keputusan-pk', [
                'periode' => $this->periode,
                'group' => $this->group,
                'pesertaNik' => $this->pesertaNik,
                'pesertaNama' => $this->pesertaNama,
                'approverNama' => $this->approverNama,
                'approverRole' => $this->approverRole,
                'recipientNama' => $this->recipientNama,
                'recipientType' => $this->recipientType,
                'mustApprove' => $mustApprove,
                'isPeserta' => $isPeserta,
                'link' => $isPeserta ? url('/hr/pk-assessment') : url('/hr/pk-assessment?tab=leader'),
                'linkLabel' => $mustApprove ? 'Approve Sekarang' : ($isPeserta ? 'Lihat Penilaian Karya' : 'Buka Penilaian Karya'),
            ]);
    }
}
