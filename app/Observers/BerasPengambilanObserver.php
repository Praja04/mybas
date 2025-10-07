<?php

namespace App\Observers;

use App\BerasPengambilan;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormBeras\BerasPengambilanAlert;

class BerasPengambilanObserver
{
    /**
     * Handle the beras pengambilan "created" event.
     *
     * @param  \App\BerasPengambilan  $berasPengambilan
     * @return void
     */
    public function created(BerasPengambilan $berasPengambilan)
    {
        //
    }

    /**
     * Handle the beras pengambilan "updated" event.
     *
     * @param  \App\BerasPengambilan  $berasPengambilan
     * @return void
     */
    public function updated(BerasPengambilan $berasPengambilan)
    {
        if ($berasPengambilan->jumlah_pengambilan_sebelum <= 20) {
            $emailTujuan = [
                'nurdiansyahjoyo@gmail.com',
                // 'indra.bayu@pt-bas-id.com@gmail.com',
                // 'tashya.claudea@pt-pas-id.com',
                // 'heri.lesmana@prakarsaalamsegar.com'
            ];

            foreach ($emailTujuan as $emailTujuan) { //100
                // dd(config('queue.connections'));
                Mail::to($emailTujuan)->queue(new BerasPengambilanAlert($berasPengambilan));
            }
        }
    }

    /**
     * Handle the beras pengambilan "deleted" event.
     *
     * @param  \App\BerasPengambilan  $berasPengambilan
     * @return void
     */
    public function deleted(BerasPengambilan $berasPengambilan)
    {
        //
    }

    /**
     * Handle the beras pengambilan "restored" event.
     *
     * @param  \App\BerasPengambilan  $berasPengambilan
     * @return void
     */
    public function restored(BerasPengambilan $berasPengambilan)
    {
        //
    }

    /**
     * Handle the beras pengambilan "force deleted" event.
     *
     * @param  \App\BerasPengambilan  $berasPengambilan
     * @return void
     */
    public function forceDeleted(BerasPengambilan $berasPengambilan)
    {
        //
    }
}
