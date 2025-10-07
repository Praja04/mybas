<?php

use App\Models\Sigra\KontrakVendor;
use Illuminate\Database\Seeder;

class SigraKontrakVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kontrak_vendor = new KontrakVendor;
        $kontrak_vendor->id_vendor = 1;
        $kontrak_vendor->id_perusahaan = 1;
        $kontrak_vendor->tanggal_mulai = date('Y-m-d');
        $kontrak_vendor->tanggal_selesai = date('Y-m-d', strtotime( date('Y-m-d') . ' + 10 Days'));
        $kontrak_vendor->save();

        $kontrak_vendor = new KontrakVendor;
        $kontrak_vendor->id_vendor = 2;
        $kontrak_vendor->id_perusahaan = 1;
        $kontrak_vendor->tanggal_mulai = date('Y-m-d');
        $kontrak_vendor->tanggal_selesai = date('Y-m-d', strtotime( date('Y-m-d') . ' + 10 Days'));
        $kontrak_vendor->save();
    }
}
