<?php

namespace App\Mail\HaloSecurity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddBaSopSupir extends Mailable
{
    use Queueable, SerializesModels;

    public $createbasopsupir;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($createbasopsupir)
    {
        $this->createbasopsupir = $createbasopsupir;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('(Halo Security) New Berita Acara S.O.P Supir')->view('mail.halo-security.add-ba-sop-supir');
    }
}
