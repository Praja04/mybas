<?php

namespace App\Mail;

use App\SpPelanggaran;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SpNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $sp;
    public $notificationType;
    public $tries = 1;
    public $timeout = 30;

    public function __construct(SpPelanggaran $sp, $notificationType = 'general')
    {
        $this->sp = $sp;
        $this->notificationType = $notificationType;
    }

    public function build()
    {
        $spNum = $this->sp->nomor_sp_generated ?: $this->sp->no_sp ?: 'Draft';
        $empName = $this->sp->employee->nama ?? 'Karyawan';

        if ($this->notificationType === 'pending_dh') {
            $subject = 'Approval Surat Peringatan - Mybas Online';
            return $this->subject($subject)
                        ->view('emails.sp_approval_notification', ['sp' => $this->sp]);
        }

        // Default / Final Approved Official Email
        $subject = 'Surat Peringatan PT BAS - ' . $spNum;
        $htmlContent = view('emails.sp_template_official', ['sp' => $this->sp])->render();

        $pdf = PDF::loadHTML($htmlContent);
        $pdfFileName = 'Surat_Peringatan_' . str_replace(['/', '\\', ' '], '_', $spNum) . '.pdf';

        return $this->subject($subject)
                    ->view('emails.sp_template_official', ['sp' => $this->sp])
                    ->attachData($pdf->output(), $pdfFileName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
