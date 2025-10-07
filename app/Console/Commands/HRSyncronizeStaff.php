<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HRSyncronizeStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:syncronize-staff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize data staff with payroll';

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
        $this->info('Syncronizing data...');
        $controller = app()->make('App\Http\Controllers\HR\StaffController');
        app()->call([$controller, 'syncronizeData'], []);
        $this->info('Syncronizing data done.');
    }
}
