<?php

namespace App\Mail\Sigra;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SIO extends Mailable
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
        return $this->subject('Pengingat: SIO Akan Segera Berakhir')
            ->view('mail.sigra.sio', [
                'sertifikasi' => $this->sertifikasi
            ]);
    }
}
