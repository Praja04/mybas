<?php

namespace App\Mail\HRConnect;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FyiGaMail extends Mailable
{
    use Queueable, SerializesModels;
    public $hitung_karyawan_baru;
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hitung_karyawan_baru, $link)
    {
        $this->hitung_karyawan_baru = $hitung_karyawan_baru;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['hitung_karyawan_baru'] = $this->hitung_karyawan_baru;
        $data['link'] = $this->link;

        return $this->subject('HRConnect - Dear GA Department')
                    ->view('mail.hr-connect.FyiToGa', $data);
    }
}
