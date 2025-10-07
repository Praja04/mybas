<?php

namespace App\Mail\HaloSecurity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddLaporanKejadian extends Mailable
{
    use Queueable, SerializesModels;

    public $createbalaporankejadian;
    public $lk_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($createbalaporankejadian, $lk_id)
    {
        $this->createbalaporankejadian = $createbalaporankejadian;
        $this->lk_id = $lk_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('(Halo Security) New Berita Acara Laporan Kejadian')->view('mail.halo-security.add-laporan-kejadian');
    }
}
