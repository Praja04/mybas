<?php

namespace App\Jobs;

use App\Mail\BufferStockReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\BerasPicGa;

class SendBufferStockReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jumlahPengambilan;

    public function __construct($jumlahPengambilan)
    {
        $this->jumlahPengambilan = $jumlahPengambilan;
    }

    public function handle()
    {
        $emails = BerasPicGa::getActiveEmails();
        Mail::to($emails)->send(new BufferStockReminder($this->jumlahPengambilan));
    }
}
