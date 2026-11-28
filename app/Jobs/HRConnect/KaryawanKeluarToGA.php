<?php
namespace App\Jobs\HRConnect;

use App\Mail\HRConnect\FyiGaShiftingOutMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class KaryawanKeluarToGA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $dataList;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $dataList)
    {
        $this->to       = $to;
        $this->dataList = $dataList;
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
        // ->send(new FyiGaShiftingOutMail($this->link));

        $internalMails  = [];
        $eksternalMails = [];

        foreach ($this->to as $email) {
            if (Str::endsWith($email, ["@myemail.pas", "@prakarsaalamsegar.com"])) {
                $internalMails[] = $email;
            } else {
                $eksternalMails[] = $email;
            }
        }

        if (count($internalMails) > 0) {
            Mail::mailer(setEmail($internalMails[0]))
                ->to($internalMails)
                ->send(new FyiGaShiftingOutMail($this->dataList));
        }

        if (count($eksternalMails) > 0) {
            Mail::to($eksternalMails)
                ->send(new FyiGaShiftingOutMail($this->dataList));
        }
    }
}
