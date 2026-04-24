<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Loker\Penghuni;
use App\Models\Loker\Rak;
use Faker\Generator as Faker;

$factory->define(Rak::class, function (Faker $faker) {
    return [
        'kode_rak'           => $faker->randomElement(['LP', 'LW']),
        'kode_blok'          => $faker->randomElement(['A1', 'A2', 'B1', 'B2']),
        'kapasitas'          => 2,
        'no_loker'           => $faker->unique()->numberBetween(1, 500),
        'gender'             => 'L',
        'is_active'          => 'Y',
        'keterangan_kondisi' => 'Bagus',
    ];
});

$factory->define(Penghuni::class, function (Faker $faker) {
    return [
        'nik'       => $faker->numerify('2024####'),
        'nama'      => $faker->name,
        'divisi'    => $faker->randomElement(['IT', 'HRGA', 'PROD', 'WH',
        ]),
        'tgl_masuk' => now(),
        'is_active' => 'L',
    ];
});
