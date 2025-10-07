<?php

namespace App\Console\Commands\HR;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:archive-absensi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan archive data absensi';

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
        // Yesterday
        // $startDate = date('Y-m-d', strtotime('-1 day')) . ' 05:00:00'; 
        // Get all live data
        $data = DB::connection('absensi_server')
        ->table('t_absensi_raw')
        // ->where('created_at', '<', $startDate)
        ->get();

        // Save data to archive table
        foreach ($data as $value) {
            $id = $value->id;

            // Exclude id
            unset($value->id);

            // Insert to archive table
            DB::connection('absensi_server')
            ->table('t_absensi_raw_archive')
            ->insert((array) $value);

            // Delete from live table
            DB::connection('absensi_server')
            ->table('t_absensi_raw')
            ->where('id', $id)
            ->delete();
        }

        $this->info($data->count() . ' data archived');
        return 0;
    }
}
