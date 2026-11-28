<?php

use App\ParkingHistories;
use Illuminate\Database\Seeder;

class ParkingHistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ParkingHistories::class, 10)->create();
    }
}
