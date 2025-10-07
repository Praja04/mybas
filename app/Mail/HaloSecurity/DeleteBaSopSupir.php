<?php

namespace App\Mail\HaloSecurity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteBaSopSupir extends Mailable
{
    use Queueable, SerializesModels;

    public $supir;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($supir)
    {
        $this->supir = $supir;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('(Halo Security) Delete Berita Acara S.O.P Supir')->view('mail.halo-security.delete-ba-sop-supir');
    }
}
