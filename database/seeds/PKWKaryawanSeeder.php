<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PKWKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$date = Carbon::now();
    	$faker = Faker::create('id_ID');
    	for ($i=1; $i <= 5; $i++) {
	        DB::table('pkw_karyawan')->insert([
	            'nik_ktp' => Str::random(10),
	            'nama' => Str::random(10),
	            'tempat_lahir' => Str::random(10),
	            'tanggal_lahir' => $date->addWeeks(rand(1, 52))->format('Y-m-d'),
	            'tanggal_masuk' => $date->addWeeks(rand(1, 52))->format('Y-m-d'),
	            'status_perdata' => $faker->randomElement(['Menikah','Belum Menikah', 'Janda', 'Duda']),
	            'agama' => 'Islam',
	            'nama_ayah' => Str::random(10),
	            'nama_ibu' => Str::random(10),
	            'nama_kontak_darurat' => Str::random(10),
	            'hubungan_kontak_darurat' => Str::random(10),
	            'no_telepon_kontak_darurat' => Str::random(10),
	            'keterangan_kartu_bpjs_ketenagakerjaan' => $faker->randomElement(['Dapat','Belum Dapat', 'Hilang']),
	            'keterangan_kartu_bpjs_kesehatan' => $faker->randomElement(['Dapat','Belum Dapat', 'Hilang']),
	            'jenis_kelamin' => $faker->randomElement(['L','P']),
	        ]);
	    }
    }
}
