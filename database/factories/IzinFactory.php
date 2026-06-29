<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\HrKaryawan;
use App\LunchBreak;
use Faker\Generator as Faker;

$factory->define(LunchBreak::class, function (Faker $faker) {
    $karyawan = HrKaryawan::inRandomOrder()->first();
    
    // Tentukan tanggal acak dari 30 hari ke belakang sampai hari ini
    $randomDate = $faker->dateTimeBetween('-30 days', 'today');
    
    // Tentukan jam keluar antara 10:00 s.d 13:59 di tanggal acak tersebut
    $jamKeluar = (clone $randomDate)->setTime($faker->numberBetween(10, 13), $faker->numberBetween(0, 59), 0);
    
    // 80% kemungkinan dia sudah kembali, 20% masih di luar / lupa tap masuk
    $isReturned = $faker->boolean(80);
    $jamMasuk = null;
    $status = 'Belum Kembali';
    $menitTerlambat = 0;

    if ($isReturned) {
        // Waktu balik acak antara 30 menit sampai 90 menit setelah keluar
        $jamMasuk = (clone $jamKeluar)->modify('+' . $faker->numberBetween(30, 90) . ' minutes');
        
        $limitTime = (clone $jamKeluar)->modify('+60 minutes');
        // lastReturnTime harus ngikutin tanggal dari jamKeluar, jangan pakai today()
        $lastReturnTime = (clone $jamKeluar)->setTime(13, 0, 0);
        
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
