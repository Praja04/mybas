<?php

namespace App\Jobs\HRConnect;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\HRConnect\FinalFyiHrMail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class KaryawanKeluarSelesaiToHR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $data;
    public $link;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $data, $link)
    {
        $this->to = $to;
        $this->data = $data;
        $this->link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mail::to($this->to)
        // ->cc($this->to)
        // ->send(new FinalFyiHrMail($this->data, $this->link));
        
        $internalMails = [];
        $eksternalMails = [];

        foreach ($this->to as $email) {
            if (strpos($email, '@myemail.pas') == true || strpos($email, '@prakarsaalamsegar.com') == true) {
                $internalMails[] = $email;
            } else {
                $eksternalMails[] = $email;
            }
        }
            
        if(count($internalMails) > 0) {
            Mail::mailer(setEmail($internalMails[0]))
            ->to($internalMails)
            ->send(new FinalFyiHrMail($this->data, $this->link));
        }
        
        if(count($eksternalMails) > 0) {
            Mail::to($eksternalMails)
            ->send(new FinalFyiHrMail($this->data, $this->link));
        }
    }
}
