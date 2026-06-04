<?php
namespace App\Mail\HRConnect;

use App\Exports\HRConnect\KaryawanFinalOnboardExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class GoodieNotifyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $count;
    public $tgl_masuk;
    // public $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($count, $tgl_masuk)
    {
        $this->count     = $count;
        $this->tgl_masuk = $tgl_masuk;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tgl_format = \Carbon\Carbon::parse($this->tgl_masuk)->format('d-m-Y');
        $nama_file  = "Final_Onboarding_{$tgl_format}.xlsx";

        return $this->subject('HRConnect - Pemberitahuan Jumlah Goodie Bag dan APD')
            ->view('mail.hr-connect.FyiGoodieApd')
            ->with([
                'count'     => $this->count,
                'tgl_masuk' => $this->tgl_masuk,
            ])
            ->attach(Excel::download(
                new KaryawanFinalOnboardExport($this->tgl_masuk),
                $nama_file
            )->getFile(), ['as' => $nama_file]);
    }
}
