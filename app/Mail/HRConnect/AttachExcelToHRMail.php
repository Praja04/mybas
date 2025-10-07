<?php

namespace App\Mail\HRConnect;

use App\HrKaryawan;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exports\HRConnect\KaryawanBaruExport;

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
        // $hr_karyawan = collect($this->data);
        // $url = public_path('images/hr_connect.jpg');
        $nama_file = 'Lampiran Karyawan Baru' . date('d-m-Y') . '.xlsx';

        return $this->subject('HRConnect - File Lampiran Karyawan Baru')
            ->view('mail.hr-connect.attachExcelToHr')
            // ->with(['data' => $hr_karyawan])
            // ->attach(
            //     (new KaryawanBaruExport($this->data))->download($nama_file),
            //     ['as' => $nama_file]
            // );
            ->attach(Excel::download(
                new KaryawanBaruExport($this->data),
                $nama_file
                )->getFile(), ['as' => $nama_file]
            );
    }
}
