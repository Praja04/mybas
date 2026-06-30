<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\HrKaryawan;
use App\LunchBreak;
use Faker\Generator as Faker;

$factory->define(LunchBreak::class, function (Faker $faker) {
    $karyawan = HrKaryawan::inRandomOrder()->first();
    
    // 30% kemungkinan di hari ini, 70% acak dari 30 hari ke belakang sampai kemarin
    if ($faker->boolean(30)) {
        $randomDate = new \DateTime('today');
    } else {
        $randomDate = $faker->dateTimeBetween('-30 days', '-1 days');
    }
    
    // Pilih shift secara acak
    $shift = $faker->randomElement([1, 2, 3]);

    if ($shift == 1) {
        // 11:30 s/d 12:59
        $hour = $faker->randomElement([11, 12]);
        $minute = $hour == 11 ? $faker->numberBetween(30, 59) : $faker->numberBetween(0, 59);
        $batasJam = 13;
    } elseif ($shift == 2) {
        // 17:30 s/d 18:59
        $hour = $faker->randomElement([17, 18]);
        $minute = $hour == 17 ? $faker->numberBetween(30, 59) : $faker->numberBetween(0, 59);
        $batasJam = 19;
    } else {
        // 01:30 s/d 02:59
        $hour = $faker->randomElement([1, 2]);
        $minute = $hour == 1 ? $faker->numberBetween(30, 59) : $faker->numberBetween(0, 59);
        $batasJam = 3;
    }
    
    $jamKeluar = (clone $randomDate)->setTime($hour, $minute, 0);
    
    // 80% kemungkinan dia sudah kembali, 20% masih di luar / lupa tap masuk
    $isReturned = $faker->boolean(80);
    $jamMasuk = null;
    $status = 'Belum Kembali';
    $menitTerlambat = 0;

    if ($isReturned) {
        // Waktu balik acak antara 30 menit sampai 90 menit setelah keluar
        $jamMasuk = (clone $jamKeluar)->modify('+' . $faker->numberBetween(30, 90) . ' minutes');
        
        $limitTime = (clone $jamKeluar)->modify('+60 minutes');
        // lastReturnTime ngikutin tanggal dari jamKeluar, sesuai $batasJam shift
        $lastReturnTime = (clone $jamKeluar)->setTime($batasJam, 0, 0);
        
        $strictLimit = $limitTime < $lastReturnTime ? $limitTime : $lastReturnTime;
        
        if ($jamMasuk > $strictLimit) {
            $status = 'Terlambat';
            $menitTerlambat = round(($jamMasuk->getTimestamp() - $strictLimit->getTimestamp()) / 60);
        } else {
            $status = 'Tepat Waktu';
        }
    }

    return [
        'nik'             => $karyawan->nik ?? $faker->numerify('######'),
        'nama'            => $karyawan->nama ?? $faker->name,
        'divisi'          => $karyawan->kode_divisi ?? 'IT',
        'jam_keluar'      => $jamKeluar->format('Y-m-d H:i:s'),
        'jam_masuk'       => $jamMasuk ? $jamMasuk->format('Y-m-d H:i:s') : null,
        'menit_terlambat' => $menitTerlambat,
        'status'          => $status,
    ];
});
