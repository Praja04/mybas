<?php

namespace App\Observers;

use App\BerasJumlah;
use App\BerasPengambilan;
use App\Mail\FormBeras\ReminderJumlahStockBeras;
use Illuminate\Support\Facades\Mail;

class BerasJumlahObserver
{
    /**
     * Handle the beras jumlah "created" event.
     *
     * @param  \App\BerasJumlah  $berasJumlah
     * @return void
     */
    public function created(BerasJumlah $berasJumlah)
    {
        //
    }

    /**
     * Handle the beras jumlah "updated" event.
     *
     * @param  \App\BerasJumlah  $berasJumlah
     * @return void
     */
    public function updated(BerasJumlah $berasJumlah)
    {
        if ($berasJumlah->jumlah_stok <= 20) {
            $emailTujuan = [
                'nurdiansyahjoyo@gmail.com',
                // 'indra.bayu@pt-bas-id.com@gmail.com',
                // 'tashya.claudea@pt-pas-id.com',
                // 'heri.lesmana@prakarsaalamsegar.com'
            ];
            foreach ($emailTujuan as $emailTujuan) { //100
                // dd(config('queue.connections'));
                Mail::to($emailTujuan)->queue(new ReminderJumlahStockBeras($berasJumlah));
            }
        }
    }

    /**
     * Handle the beras jumlah "deleted" event.
     *
     * @param  \App\BerasJumlah  $berasJumlah
     * @return void
     */
    public function deleted(BerasJumlah $berasJumlah)
    {
        //
    }

    /**
     * Handle the beras jumlah "restored" event.
     *
     * @param  \App\BerasJumlah  $berasJumlah
     * @return void
     */
    public function restored(BerasJumlah $berasJumlah)
    {
        //
    }

    /**
     * Handle the beras jumlah "force deleted" event.
     *
     * @param  \App\BerasJumlah  $berasJumlah
     * @return void
     */
    public function forceDeleted(BerasJumlah $berasJumlah)
    {
        //
    }
}
