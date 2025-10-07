<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\StockReminder;
use Illuminate\Support\Facades\Mail; 
use Exception;

class SendStockReminderEmailJumlahBeras implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newJumlahStock;
    protected $emailTujuan;

    public function __construct($newJumlahStock, $emailTujuan)
    {
        $this->newJumlahStock = $newJumlahStock;
        $this->emailTujuan = $emailTujuan;
    }

    public function handle()
    {
        foreach ($this->emailTujuan as $email) {
            try {
                Mail::to($email)->send(new StockReminder($this->newJumlahStock));
            } catch (Exception $e) {
                error_log("Failed to send email to $email: " . $e->getMessage());
            }
        }
    }
}
