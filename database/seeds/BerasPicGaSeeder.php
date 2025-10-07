<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BerasPicGaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('beras_pic_ga')->insert([
            ['nama' => 'Indra Bayu', 'email' => 'indra.bayu@pt-bas-id.com', 'is_active' => 'Y'],
            ['nama' => 'Joyo Nurdiansyah', 'email' => 'adamfahrisal26@gmail.com', 'is_active' => 'Y'],
            ['nama' => 'Tashya Claudea', 'email' => 'tashya.claudea@pt-pas-id.com', 'is_active' => 'Y'],
            ['nama' => 'Heri Lesmana', 'email' => 'heri.lesmana@prakarsaalamsegar.com', 'is_active' => 'Y'],
            ['nama' => 'Khodrirhomadoni', 'email' => 'khodrirhomadoni.k@pt-bas-id.com', 'is_active' => 'Y'],
        ]);
    }
}
