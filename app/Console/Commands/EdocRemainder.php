<?php

namespace App\Console\Commands;

use App\Mail\EDoc\RemainderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EdocRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'edoc:remaind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remainder edoc log than not alredy done';

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
        $this->info('Get data edoc yang belum diambil');
        $data = DB::table('edoc_kedatangan')
        ->where('status', 1)
        ->where('tanggal_kedatangan', '<', date('Y-m-d'))
        ->get();

        foreach($data as $d) {
            $pic = DB::table('edoc_pic')->where('dept', $d->dept_penerima)->get();
            $to = '';
            $cc = [];
            
            foreach($pic as $key => $_pic) {
                $email = DB::table('users')->where('username', $_pic->nik)->first()->email;
                if($key == 0) {
                    $to = $email;
                } else {
                    $cc[] = $email;
                }
            }

            Mail::mailer(setEmail($to))
            ->to($to)
            ->cc($cc)
            ->send(new RemainderMail($d));
        }

        return 0;
    }
}
