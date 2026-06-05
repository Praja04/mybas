<?php

namespace App\Jobs\HRConnect;

use App\Mail\HRConnect\GoodieNotifyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GoodieNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $count;
    public $tgl_masuk;
    public $link;
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
            if (Str::endsWith($email, ['@myemail.pas', '@prakarsaalamsegar.com'])) {
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
