<?php

use App\Models\Sigra\Operasional;
use Illuminate\Database\Seeder;

class SigraOperasionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $operasional = new Operasional;
        $operasional->id_perusahaan = 1;
        $operasional->nama_perizinan = 'Izin Pemakaian Motor Diesel ( Genset ) BMGS 1900 KVA';
        $operasional->nomor_perizinan = '566/1980/Wasker/IX/2011';
        $operasional->save();

        $operasional = new Operasional;
        $operasional->id_perusahaan = 1;
        $operasional->nama_perizinan = 'SLO Genset Motor Diesel ( Genset ) BMGS 1900 KVA';
        $operasional->nomor_perizinan = '254.O.PP.171.3275.000019';
        $operasional->save();
    }
}
