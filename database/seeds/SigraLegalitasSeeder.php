<?php

use App\Models\Sigra\Legalitas;
use Illuminate\Database\Seeder;

class SigraLegalitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $legalitas = new Legalitas();
        $legalitas->id_perusahaan = 1;
        $legalitas->nama_legalitas = 'Izin Pelaksanaan Pendirian Bangunan';
        $legalitas->save();

        $legalitas = new Legalitas();
        $legalitas->id_perusahaan = 1;
        $legalitas->nama_legalitas = 'Izin Mendirikan Bangunan (IMB)';
        $legalitas->save();
    }
}
