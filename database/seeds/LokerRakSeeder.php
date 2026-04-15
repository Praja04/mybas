<?php

use App\Models\Loker\Rak;
use Illuminate\Database\Seeder;

class LokerRakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rak::truncate();

        for ($i = 1; $i <= 20; $i++) {
            Rak::create([
                'kode_rak'  => 'LP',
                'kode_blok' => ($i <= 10) ? 'BLOK A' : 'BLOK B',
                'no_loker'  => $i,
                'gender'    => 'L',
                'kapasitas' => 2,
                'is_active' => 'Y',
            ]);
        }

        for ($i = 1; $i <= 20; $i++) {
            Rak::create([
                'kode_rak'  => 'LW',
                'kode_blok' => ($i <= 10) ? 'BLOK C' : 'BLOK D',
                'no_loker'  => $i,
                'gender'    => 'P',
                'kapasitas' => 2,
                'is_active' => 'Y',
            ]);
        }
    }
}
