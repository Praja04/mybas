<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Cek sigra expired every mondey at 07:00
        $schedule->command('sigra:check-kontrak-vendor')->mondays()->at('07:00');
        $schedule->command('sigra:check-legalitas')->mondays()->at('07:00');
        $schedule->command('sigra:check-operasional')->mondays()->at('07:00');
        $schedule->command('sigra:check-sh-bahan-baku')->mondays()->at('07:00');
        $schedule->command('sigra:check-sio')->mondays()->at('07:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
