<?php
namespace App\Mail\HRConnect;

use App\Exports\HRConnect\KaryawanKeluarFromAdminToGAExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class FyiGaShiftingOutMail extends Mailable
{
    use Queueable, SerializesModels;
    public $dataList;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataList)
    {
        $this->dataList = $dataList;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tgl_now   = date('d-m-Y');
        $nama_file = "Lampiran Karyawan Keluar per Tanggal {$tgl_now}.xlsx";

        return $this
            ->subject('HRConnect - Informasi Permohonan Penghapusan Data Karyawan')
            ->view('mail.hr-connect.FyiToGaShiftOut', [
                'list_karyawan' => $this->dataList['list_karyawan'],
                'link'          => $this->dataList['tautan'],
            ])
            ->attach(Excel::download(
                new KaryawanKeluarFromAdminToGAExport($this->dataList['list_karyawan']), $tgl_now, $nama_file
            )->getFile(), ['as' => $nama_file]);
    }
}
