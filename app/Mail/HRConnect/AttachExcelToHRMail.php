<?php
namespace App\Mail\HRConnect;

use App\Exports\HRConnect\KaryawanBaruExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class AttachExcelToHRMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tgl_now   = date('d-m-Y');
        $nama_file = "Lampiran Karyawan Baru per Tanggal {$tgl_now}.xlsx";

        return $this->subject('HRConnect - File Lampiran Karyawan Baru')
            ->view('mail.hr-connect.attachExcelToHr')
            ->attach(Excel::download(
                new KaryawanBaruExport($this->data),
                $nama_file
            )->getFile(), ['as' => $nama_file]
            );
    }
}
