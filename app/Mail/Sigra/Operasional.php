<?php

namespace App\Mail\Sigra;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Operasional extends Mailable
{
    use Queueable, SerializesModels;

    private $sertifikasi;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sertifikasi)
    {
        $this->sertifikasi = $sertifikasi;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pengingat: Sertifikasi Operasional Akan Segera Berakhir')->view('mail.sigra.operasional', [
            'sertifikasi' => $this->sertifikasi
        ]);
    }
}
