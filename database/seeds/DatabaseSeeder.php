<?php

use Illuminate\Database\Seeder;
use LokerPenghuniSeeder;
use LokerRakSeeder;
use SigraSertifikasiOperasionalSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(PKWKaryawanSeeder::class);
        //        $this->call(SigraMasterVendorSeeder::class);
        //        $this->call(SigraPerusahaanSeeder::class);
        //        $this->call(SigraKontrakVendorSeeder::class);
        //        $this->call(SigraLegalitasSeeder::class);
        //        $this->call(SigraSertifikasiLegalitasSeeder::class);
        //        $this->call(SigraOperasionalSeeder::class);
        $this->call(SigraSertifikasiOperasionalSeeder::class);
        $this->call(LokerRakSeeder::class);
        $this->call(LokerPenghuniSeeder::class);
    }
}
