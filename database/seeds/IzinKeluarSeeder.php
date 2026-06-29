<?php

use App\LunchBreak;
use Illuminate\Database\Seeder;

class IzinKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LunchBreak::truncate();
        factory(LunchBreak::class, 50)->create();
    }
}
