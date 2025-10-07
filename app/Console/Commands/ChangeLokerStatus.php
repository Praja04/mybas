<?php

namespace App\Console\Commands;

use App\Models\HR\NewHire\Loker;
use Illuminate\Console\Command;

class ChangeLokerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loker:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek status loker, dan nonaktifkan yang sudah expired';

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
        // Get loker status 1
        $loker_aktif = Loker::whereIn('status', ['1', '1-0'])->get();

        // Looping loker
        foreach ($loker_aktif as $l) {
            // Cek expired date
            if (date('Y-m-d') > $l->tanggal_selesai) {
                // Update status loker
                // Show some info
                $this->info('Loker ' . $l->uuid . ' expired');

                $firestore = app('firebase.firestore')->database();

                $job_vacancy = $firestore->collection('job_vacancy');

                $job_vacancy->document($l->uuid)->update([
                    [ 'path' => 'status', 'value'=> '1-0']
                ]);

                $l->update([
                    'status' => '1-0'
                ]);
            }
        }

        $loker_tidak_aktif = Loker::whereIn('status', ['2', '3'])->get();

        // Looping loker
        foreach ($loker_tidak_aktif as $l) {
            $firestore = app('firebase.firestore')->database();

            $job_vacancy = $firestore->collection('job_vacancy');

            // Check if the document exist
            $document = $job_vacancy->document($l->uuid)->snapshot()->exists();

            if (!$document) {
                $this->info('Loker ' . $l->uuid . ' tidak aktif');

                // $l->delete();

                continue;
            }

            $job_vacancy->document($l->uuid)->update([
                [ 'path' => 'status', 'value'=> $l->status]
            ]);
        }

        return 0;
    }
}
