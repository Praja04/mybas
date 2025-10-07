<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\BerasPengambilan;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\BerasPicGa;

class BufferStockReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $jumlah_pengambilan_sesudah;
    public $berasjumlahBufferStock;

    public function __construct($jumlah_pengambilan_sesudah)
    {
        $this->jumlah_pengambilan_sesudah = $jumlah_pengambilan_sesudah;
        $this->berasjumlahBufferStock = BerasPengambilan::latest()->take(5)->get();
    }

    public function build()
    {
        $emails = BerasPicGa::getActiveEmails();

        return $this->subject('Pengingat Stock Beras')
                    ->view('mail.reminder-pengeluaran-beras')
                    ->with([
                        'jumlah_pengambilan_sesudah' => $this->jumlah_pengambilan_sesudah,
                        'berasjumlahBufferStock' => $this->berasjumlahBufferStock
                    ])
                    ->to($emails);
    }
}
