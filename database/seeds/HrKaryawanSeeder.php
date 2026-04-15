<?php

use App\Models\HR\Karyawan;
use Illuminate\Database\Seeder;

class HrKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Karyawan::class, 50)->create();
    }
}
