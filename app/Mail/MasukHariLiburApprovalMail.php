<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MasukHariLiburApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailContent;
    public $data;

    public function __construct($emailContent, $data)
    {
        $this->emailContent = $emailContent;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Pemberitahuan Persetujuan Hari Libur')
            ->from('ptpasapplication2@gmail.com', 'PT.BAS NOTIFICATION')
            ->view('hr.masukharilibur.email.approval_hari_libur_email')
            ->with([
                'content' => $this->emailContent,
                'data' => $this->data,
            ]);
    }
}
