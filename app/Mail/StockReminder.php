<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\BerasJumlah; 
use Illuminate\Contracts\Queue\ShouldQueue;

class StockReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $jumlah_stock_sesudah;
    public $berasJumlahData;

    public function __construct($jumlah_stock_sesudah)
    {
        $this->jumlah_stock_sesudah = $jumlah_stock_sesudah;
        $this->berasJumlahData = BerasJumlah::latest()->take(5)->get();
    }

    public function build()
    {
        return $this->subject('Pengingat Stock Beras')
                    ->view('mail.reminder-stock-beras')
                    ->with([
                        'jumlah_stock_sesudah' => $this->jumlah_stock_sesudah,
                        'berasJumlahData' => $this->berasJumlahData
                    ])
                    ->to([
                        'nurdiansyahjoyo@gmail.com',
                        'indra.bayu@pt-bas-id.com',
                        'tashya.claudea@pt-pas-id.com',
                        'heri.lesmana@prakarsaalamsegar.com'
                    ]);
    }
}
