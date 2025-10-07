<?php

namespace App\Jobs\HRConnect;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\HRConnect\AttachExcelToHRMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class KaryawanMasukToHR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $data)
    {
        $this->to = $to;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $internalMails = [];
        $eksternalMails = [];

        foreach ($this->to as $email) {
            if (strpos($email, '@myemail.pas') == true || strpos($email, '@prakarsaalamsegar.com') == true) {
                $internalMails[] = $email;
            } else {
                $eksternalMails[] = $email;
            }
        }
            
        // Email Internal
        if(count($internalMails) > 0) {
            Mail::mailer(setEmail($internalMails[0]))
            ->to($internalMails)
            ->send(new AttachExcelToHRMail($this->data));
        }
        
        // Email Eksternal
        if(count($eksternalMails) > 0) {
            Mail::to($eksternalMails)
            ->send(new AttachExcelToHRMail($this->data));
        }
    }
}
