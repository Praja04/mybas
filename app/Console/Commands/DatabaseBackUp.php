<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';

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
        // Clean directory before backup
        $this->info("Cleaning directory...");
        $file = new Filesystem;
        $file->cleanDirectory('storage/app/backup');

        $this->info("Backuping...");
        $databases = [
            'absensi',
            'app_mixer_v2_4',
            'birthday',
            'ite_system',
            'dashboard',
            'kas',
            'mrbs',
            'scan_suhu',
            'wcp'
        ];

        foreach($databases as $database) {
            $this->call('database:dobackup', [
                'db' => $database
            ]);
        }

        return NULL;
    }
}
