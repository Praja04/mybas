<?php
namespace App\Mail\HRConnect;

use App\Exports\HRConnect\KaryawanKeluarFromGAToHrKaryawan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class FinalFyiHrMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $link)
    {
        $this->data = $data;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        // $data = collect($this->data)->pluck('checklistId');
        $tgl_now      = date('d-m-Y');
        $nama_file    = "Lampiran Karyawan Keluar per Tanggal {$tgl_now}.xlsx";
        $data['link'] = $this->link;

        return $this->subject('HRConnect - Pemberitahuan Data Karyawan Yang Keluar')
            ->view('mail.hr-connect.FyiFinalToHR', $data)
            ->attach(Excel::download(
                new KaryawanKeluarFromGAToHrKaryawan($this->data),
                $nama_file
            )->getFile(), ['as' => $nama_file]
            );
    }
}
