<?php

namespace App\Mail\HaloSecurity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddBaSopKaryawan extends Mailable
{
    use Queueable, SerializesModels;

    public $createbasopkaryawan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($createbasopkaryawan)
    {
        $this->createbasopkaryawan = $createbasopkaryawan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('(Halo Security) New Berita Acara S.O.P Karyawan')->view('mail.halo-security.add-ba-sop-karyawan');
    }
}
