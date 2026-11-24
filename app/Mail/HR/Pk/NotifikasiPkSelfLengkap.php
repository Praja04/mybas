<?php

namespace App\Mail\HR\Pk;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiPkSelfLengkap extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $group;
    protected $pesertaNik;
    protected $pesertaNama;
    protected $recipientNama;
    protected $recipientRole;

    public function __construct($periode, $group, $pesertaNik, $pesertaNama, $recipientNama, $recipientRole)
    {
        $this->periode = (string) $periode;
        $this->group = (string) $group;
        $this->pesertaNik = (string) $pesertaNik;
        $this->pesertaNama = (string) $pesertaNama;
        $this->recipientNama = (string) $recipientNama;
        $this->recipientRole = (string) $recipientRole;
    }

    public function build()
    {
        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject('Penilaian Karya Periode ' . $this->periode . ' (Group ' . $this->group . ') — Self Assessment ' . $this->pesertaNama . ' Sudah Lengkap')
            ->view('mail.hr.pk.notifikasi-pk-self-lengkap', [
                'periode' => $this->periode,
                'group' => $this->group,
                'pesertaNik' => $this->pesertaNik,
                'pesertaNama' => $this->pesertaNama,
                'recipientNama' => $this->recipientNama,
                'recipientRole' => $this->recipientRole,
                'link' => url('/hr/pk-assessment?tab=leader'),
            ]);
    }
}
