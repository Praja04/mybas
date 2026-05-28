<?php
namespace App\Mail\HRConnect;

use App\Exports\HRConnect\KaryawanKeluarExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class NotifiedOutMail extends Mailable
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
        $nama_file = 'Lampiran Karyawan Keluar' . date('d-m-Y') . '.xlsx';

        return $this->subject('HRConnect - File Lampiran Karyawan Keluar')
            ->view('mail.hr-connect.attachExcelToHrOUT')
            ->attach(Excel::download(
                new KaryawanKeluarExport($this->data),
                $nama_file
            )->getFile(), ['as' => $nama_file]
            );
    }
}
