<?php

namespace App\Mail\HRConnect;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FyiGaShiftingOutMail extends Mailable
{
    use Queueable, SerializesModels;
    public $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['link'] = $this->link;

        return $this
                ->subject('HRConnect - Informasi Permohonan Penghapusan Data Karyawan')
                ->view('mail.hr-connect.FyiToGaShiftOut', $data);
    }
}
