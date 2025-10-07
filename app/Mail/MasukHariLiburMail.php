<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;


class MasukHariLiburMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $data_count;
    protected $tanggal;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data_count, $tanggal)
    {
        //
        $this->data_count = $data_count;
        $this->tanggal = $tanggal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tanggal = $this->tanggal;
        $data = [
            'data_count' => $this->data_count,
            'title' => 'Data Karyawan Masuk Hari Libur',
            'app' => 'My BAS Online Notification'
        ];

        return $this->view('hr.masukharilibur.email.masuk_hari_libur_email', compact('data', 'tanggal'));
    }
}
