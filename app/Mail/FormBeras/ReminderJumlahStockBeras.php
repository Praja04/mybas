<?php

namespace App\Mail\FormBeras;

use App\BerasJumlah;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderJumlahStockBeras extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $berasJumlah;

    public function __construct(BerasJumlah $berasJumlah)
    {
        $this->berasJumlah = $berasJumlah;
    }

    function subject($subject)
    {
        return 'Reminder Jumlah Stock Beras';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.reminder', [
            'jumlah_saat_ini' => $this->berasJumlah->jumlah_stock
        ]);
    }
}
