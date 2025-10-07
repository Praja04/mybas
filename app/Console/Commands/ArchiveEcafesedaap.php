<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveEcafesedaap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecafesedaap:archive {dayCount=90}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive ecafesedaap data';

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
        $dayCount = $this->argument('dayCount');
        // Get data more than 30 days
        $this->info('Get data more than ' . $dayCount . ' days...');

        $data = \App\Models\HR\Ecafesedaap::where('created_at', '<', now()->subDays($dayCount))->get();

        // Archive data
        $this->info('Archiving data...');
        $archived_data = 0;
        foreach ($data as $d) {
            DB::table('t_ecafe_sedaap_archive')
            ->insert(
                $d->toArray()
            );

            $d->delete();
            $this->info('Archived: ' . $d->id);
            $archived_data++;
        }

        $this->info('Archived data: ' . $archived_data);

        return 0;
    }
}
