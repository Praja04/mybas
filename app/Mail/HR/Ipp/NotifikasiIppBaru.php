<?php

namespace App\Mail\HR\Ipp;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiIppBaru extends Mailable
{
    use Queueable, SerializesModels;

    protected $periode;
    protected $nama;
    protected $pembuat;

    public function __construct($periode, $nama, $pembuat)
    {
        $this->periode = $periode;
        $this->nama = $nama;
        $this->pembuat = $pembuat;
    }

    public function build()
    {
        return $this->from(config('pms.access_otp.from_address'), config('pms.access_otp.from_name'))
            ->subject('IPP Periode ' . $this->periode . ' — Silakan Isi Aktual')
            ->view('mail.hr.ipp.notifikasi-ipp-baru', [
                'periode' => $this->periode,
                'nama' => $this->nama,
                'pembuat' => $this->pembuat,
                'link' => url('/hr/ipp-aktual'),
            ]);
    }
}
