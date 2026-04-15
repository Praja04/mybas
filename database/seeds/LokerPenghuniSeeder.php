<?php

use App\Models\Loker\Penghuni;
use Illuminate\Database\Seeder;

class LokerPenghuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Penghuni::truncate();

        for ($i = 1; $i <= 3; $i++) {
            Penghuni::create([
                'nik'               => '2026000' . $i,
                'nama'              => 'User Dummy ' . $i,
                'divisi'            => 'IT',
                'kode_rak'          => 'LP',
                'no_loker'          => $i,
                'kategori_karyawan' => 'non_staff',
                'tgl_masuk'         => now(),
                'is_active'         => 'Y',
            ]);
        }
    }
}
