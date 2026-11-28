<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IzinKeluarReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $filePath;
    protected $fileName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filePath, $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Laporan Riwayat Istirahat Karyawan')
            ->html('<h3>Halo,</h3><p>Berikut dilampirkan Laporan Riwayat Istirahat Karyawan hasil penyaringan dari sistem.</p><p>Terima kasih.</p>')
            ->attach($this->filePath, [
                'as' => $this->fileName,
            ]);
    }
}
