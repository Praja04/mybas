<?php

namespace App\Mail;

use App\SpPelanggaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SpNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $sp;
    public $notificationType;

    public function __construct(SpPelanggaran $sp, $notificationType = 'general')
    {
        $this->sp = $sp;
        $this->notificationType = $notificationType;
    }

    public function build()
    {
        $subject = 'Notifikasi SP - ' . ($this->sp->nomor_sp_generated ?: $this->sp->no_sp ?: 'Draft SP');

        return $this->subject($subject)
                    ->view('emails.sp_notification');
    }
}
