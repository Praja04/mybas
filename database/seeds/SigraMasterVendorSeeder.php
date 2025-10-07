<?php

use App\Models\Sigra\MasterVendor;
use Illuminate\Database\Seeder;

class SigraMasterVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'ISS';
        $master_vendor->jenis_pekerjaan = 'Cleaning Service';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'DELTA';
        $master_vendor->jenis_pekerjaan = 'Security';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'KMJ TKBM';
        $master_vendor->jenis_pekerjaan = 'Bongkar Muat';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'DINSIH';
        $master_vendor->jenis_pekerjaan = 'Angkut sampah';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'Klinik Seto';
        $master_vendor->jenis_pekerjaan = 'klinik internal';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'Gema Catering';
        $master_vendor->jenis_pekerjaan = 'Konsumsi karyawan';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'Rien Catering';
        $master_vendor->jenis_pekerjaan = 'Konsumsi karyawan';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'GEA Catering';
        $master_vendor->jenis_pekerjaan = 'Konsumsi karyawan';
        $master_vendor->save();

        $master_vendor = new MasterVendor;
        $master_vendor->nama_vendor = 'Dana Kordinasi';
        $master_vendor->jenis_pekerjaan = 'Lingkungan Perusahaan';
        $master_vendor->save();
    }
}
