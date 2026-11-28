<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\ParkingHistories;
use Faker\Generator as Faker;

$factory->define(ParkingHistories::class, function (Faker $faker) {
    static $people = [];

    if (empty($people)) {
        for ($i = 0; $i < 50; $i++) {
            $people[] = [
                'nik'     => (string) $faker->unique()->numberBetween(100000000000, 999999999999),
                'sn_card' => (string) $faker->unique()->numberBetween(10000000, 99999999),
                'nama'    => $faker->unique()->name,
            ];
        }
    }

    $person = $faker->randomElement($people);

    return [
        'nik'       => $person['nik'],
        'sn_card'   => $person['sn_card'],
        'nama'      => $person['nama'],
        'tapped_at' => $faker->dateTime()->format('Y-m-d H:i:s'),
        'status'    => $faker->randomElement(['IN', 'OUT']),
    ];
});
