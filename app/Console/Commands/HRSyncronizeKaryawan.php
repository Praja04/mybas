<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HRSyncronizeKaryawan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:syncronize-karyawan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize data karyawan with payroll';

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
        $controller = app()->make('App\Http\Controllers\HR\KaryawanController');
        app()->call([$controller, 'syncronizeData'], []);
        $this->info('Syncronizing data done.');
    }
}
