<?php

namespace App\Jobs\HRConnect;

use Illuminate\Bus\Queueable;
use App\Mail\HRConnect\FyiAdminMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class KaryawanMasukToAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $link;
    public $hitung_karyawan_baru;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $hitung_karyawan_baru, $link)
    {
        $this->to = $to;
        $this->hitung_karyawan_baru = $hitung_karyawan_baru;
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
        // ->cc($this->to)->send(new FyiAdminMail($this->hitung_karyawan_baru, $this->link));

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
            ->send(new FyiAdminMail($this->hitung_karyawan_baru, $this->link));
        }
        
        if(count($eksternalMails) > 0) {
            Mail::to($eksternalMails)
            ->send(new FyiAdminMail($this->hitung_karyawan_baru, $this->link));
        }
    }
}
