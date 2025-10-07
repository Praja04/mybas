<?php

namespace App\Mail\HaloSecurity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteBaSopKaryawan extends Mailable
{
    use Queueable, SerializesModels;

    public $karyawan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($karyawan)
    {
        $this->karyawan = $karyawan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('(Halo Security) Delete Berita Acara S.O.P Karyawan')->view('mail.halo-security.delete-ba-sop-karyawan');
    }
}
