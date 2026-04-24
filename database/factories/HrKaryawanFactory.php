<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HR\Karyawan;
use Faker\Generator as Faker;

$factory->define(Karyawan::class, function (Faker $faker) {
    $gender = $faker->randomElement(['L', 'P']);
    return [
    'nik'           => $faker->unique()->numerify('2024####'),
    'nama'          => $faker->name($gender == 'L' ? 'male' : 'female'),
    'jenis_kelamin' => $gender,                                             // Sesuai kolom nomor 9
    'staff'         => $faker->randomElement(['Y', 'N']),                   // Sesuai kolom nomor 6
    'kode_divisi'   => $faker->randomElement(['HRGA', 'IT', 'PROD', 'WH']), // Sesuai kolom nomor 71
    'active'        => 'Y',                                                 // Sesuai kolom nomor 80
    'is_complete'   => 'Y',                                                 // Sesuai kolom nomor 2
];

});
