<?php

namespace App\Console\Commands;

use App\Jobs\SendSioEmailJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;
use App\Mail\Sigra\SIO as EmailSIO;
use Illuminate\Support\Facades\Log;

class SigraCheckSio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-sio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari SIO';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SendSioEmailJob::dispatch();

        $this->info('[SIGRA SIO] Job SIO dikirim ke queue.');
    }
}
