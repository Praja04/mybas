<?php

namespace App\Mail\HRConnect;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GoodieNotifyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $count;
    public $tgl_masuk;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($count, $tgl_masuk)
    {
        $this->count = $count;
        $this->tgl_masuk = $tgl_masuk;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('HRConnect - Pemberitahuan Jumlah Goodie Bag dan APD')
                    ->view('mail.hr-connect.FyiGoodieApd')
                    ->with([
                        'count' => $this->count,
                        'tgl_masuk' => $this->tgl_masuk
                    ]);
    }
}
