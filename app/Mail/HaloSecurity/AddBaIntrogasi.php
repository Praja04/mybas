<?php

namespace App\Mail\HaloSecurity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddBaIntrogasi extends Mailable
{
    use Queueable, SerializesModels;

    public $createbaintrogasi;
    public $bai_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($createbaintrogasi, $bai_id)
    {
        $this->createbaintrogasi = $createbaintrogasi;
        $this->bai_id = $bai_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('(Halo Security) New Berita Acara Introgasi')->view('mail.halo-security.add-ba-introgasi');
    }
}
