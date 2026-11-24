<?php

namespace App\Mail\HR\Pk;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiPenilaianKarya extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $nama;

    public function __construct($periode, $nama)
    {
        $this->periode = $periode;
        $this->nama = $nama;
    }

    public function build()
    {
        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject('Penilaian Karya Periode ' . $this->periode . ' — Silakan Mengisi')
            ->view('mail.hr.pk.notifikasi-penilaian-karya', [
                'periode' => $this->periode,
                'nama' => $this->nama,
                'link' => url('/hr/pk-assessment'),
            ]);
    }
}
