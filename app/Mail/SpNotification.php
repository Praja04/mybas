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
        $isMangkir = ($this->sp->sumber_data === 'MANGKIR');
        $jenis = $isMangkir ? 'SP Mangkir' : 'SP Pelanggaran';

        // Email ke Dept Head (saat Admin Submit)
        if (in_array($this->notificationType, ['pending_dh', 'SUBMIT_DEPT_HEAD'])) {
            $subject = '[Persetujuan Diperlukan] ' . $jenis . ' Karyawan - Mybas Online';
            return $this->subject($subject)
                        ->view('emails.sp_approval_notification', ['sp' => $this->sp]);
        }

        // Email Final ke Karyawan + Dept Head (saat IR Head Approve)
        $subject = 'Surat Peringatan Resmi PT BAS - ' . $spNum . ' (' . $empName . ')';
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
