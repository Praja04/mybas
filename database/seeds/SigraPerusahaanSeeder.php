<?php

use App\Models\Sigra\Perusahaan;
use Illuminate\Database\Seeder;

class SigraPerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perusahaan = new Perusahaan;
        $perusahaan->nama_perusahaan = 'PAS';
        $perusahaan->save();

        $perusahaan = new Perusahaan;
        $perusahaan->nama_perusahaan = 'BAS';
        $perusahaan->save();
    }
}
