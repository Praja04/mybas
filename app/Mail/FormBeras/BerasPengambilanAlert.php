<?php

namespace App\Mail\FormBeras;

use App\BerasPengambilan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BerasPengambilanAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $berasPengambilan;

    public function __construct(BerasPengambilan $berasPengambilan)
    {
        $this->berasPengambilan = $berasPengambilan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Reminder Pengeluaran Beras - Jumlah: ' . $this->berasPengambilan->jumlah_pengambilan_sebelum;

        return $this->subject($subject)
                    ->view('mail.reminder-pengeluaran-beras', [
                        'jumlah_saat_ini' => $this->berasPengambilan->jumlah_pengambilan_sebelum
                    ]);
    }
}
