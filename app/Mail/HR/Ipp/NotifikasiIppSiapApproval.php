<?php

namespace App\Mail\HR\Ipp;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiIppSiapApproval extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $tahapanLabel;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $approverNama;
    protected $approverRole;

    public function __construct($periode, $tahapanLabel, $pesertaNik, $pesertaNama, $approverNama, $approverRole)
    {
        $this->periode = $periode;
        $this->tahapanLabel = $tahapanLabel;
        $this->pesertaNik = $pesertaNik;
        $this->pesertaNama = $pesertaNama;
        $this->approverNama = $approverNama;
        $this->approverRole = $approverRole;
    }

    public function build()
    {
        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject('IPP Periode ' . $this->periode . ' (' . $this->tahapanLabel . ') — Menunggu Approval Anda')
            ->view('mail.hr.ipp.notifikasi-ipp-siap-approval', [
                'periode' => $this->periode,
                'tahapanLabel' => $this->tahapanLabel,
                'pesertaNik' => $this->pesertaNik,
                'pesertaNama' => $this->pesertaNama,
                'approverNama' => $this->approverNama,
                'approverRole' => $this->approverRole,
                'link' => url('/hr/ipp-user-approval'),
            ]);
    }
}
