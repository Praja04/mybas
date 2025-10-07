<?php

namespace App\Jobs\HRConnect;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\HRConnect\GoodieNotifyMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GoodieNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $count;
    public $tgl_masuk;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $count, $tgl_masuk)
    {
        $this->to = $to;
        $this->count = $count;
        $this->tgl_masuk = $tgl_masuk;
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
        // ->send(new GoodieNotifyMail($this->count, $this->tgl_masuk));

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
            ->send(new GoodieNotifyMail($this->count, $this->tgl_masuk));
        }
        
        if(count($eksternalMails) > 0) {
            Mail::to($eksternalMails)
            ->send(new GoodieNotifyMail($this->count, $this->tgl_masuk));
        }
    }
}
