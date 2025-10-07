<?php

use App\Models\Sigra\SertifikasiOperasional;
use Illuminate\Database\Seeder;

class SigraSertifikasiOperasionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sertifikasi = new SertifikasiOperasional;
        $sertifikasi->id_operasional = 1;
        $sertifikasi->perizinan = 'Ada';
        $sertifikasi->tanggal_sertifikasi = date('Y-m-d');
        $sertifikasi->tanggal_expired = date('Y-m-d', strtotime(date('Y-m-d' . ' + 60 Days')) );
        $sertifikasi->dokumen_asli = 'Ada';
        $sertifikasi->scan = 'ada';
        $sertifikasi->remarks = 'Ini remarks';
        $sertifikasi->tahun = date('Y');
        $sertifikasi->save();

        $sertifikasi = new SertifikasiOperasional;
        $sertifikasi->id_operasional = 2;
        $sertifikasi->perizinan = 'Ada';
        $sertifikasi->tanggal_sertifikasi = date('Y-m-d');
        $sertifikasi->tanggal_expired = date('Y-m-d', strtotime(date('Y-m-d' . ' + 60 Days')) );
        $sertifikasi->dokumen_asli = 'Ada';
        $sertifikasi->scan = 'ada';
        $sertifikasi->remarks = 'Ini remarks';
        $sertifikasi->tahun = date('Y');
        $sertifikasi->save();
    }
}
