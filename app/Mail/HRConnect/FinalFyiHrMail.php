<?php

namespace App\Mail\HRConnect;

use App\Exports\HRConnect\KaryawanKeluarFromGAToHrKaryawan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $nama_file = 'Lampiran Karyawan Keluar' . date('d-m-Y') . '.xlsx';
        $data['link'] = $this->link;
        
        return $this->subject('HRConnect - Pemberitahuan Data Karyawan Yg Sudah Terhapus')
            ->view('mail.hr-connect.FyiFinalToHR', $data)
            ->attach(Excel::download(
                new KaryawanKeluarFromGAToHrKaryawan($this->data),
                $nama_file
                )->getFile(), ['as' => $nama_file]
            );
    }
}
