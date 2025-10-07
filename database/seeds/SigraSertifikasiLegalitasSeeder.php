<?php

use App\Models\Sigra\SertifikasiLegalitas;
use Illuminate\Database\Seeder;

class SigraSertifikasiLegalitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sertifikasi = new SertifikasiLegalitas;
        $sertifikasi->id_legalitas = 1;
        $sertifikasi->nomor_dokumen = '503/0049/DTKP';
        $sertifikasi->instansi = 'DPMPTSP';
        $sertifikasi->tanggal_terbit = date('Y-m-d');
        $sertifikasi->tanggal_habis = date('Y-m-d', strtotime(date('Y-m-d') . ' + 90 Days'));
        $sertifikasi->masa_berlaku = '1 thn';
        $sertifikasi->keterangan = 'Ini sertifikasi';
        $sertifikasi->status = 'Status';
        $sertifikasi->save();

        $sertifikasi = new SertifikasiLegalitas;
        $sertifikasi->id_legalitas = 2;
        $sertifikasi->nomor_dokumen = '503/0769/I-B/Distarkim';
        $sertifikasi->instansi = 'DPMPTSP';
        $sertifikasi->tanggal_terbit = date('Y-m-d');
        $sertifikasi->tanggal_habis = date('Y-m-d', strtotime(date('Y-m-d') . ' + 90 Days'));
        $sertifikasi->masa_berlaku = '1 thn';
        $sertifikasi->keterangan = 'Ini sertifikasi';
        $sertifikasi->status = 'Status';
        $sertifikasi->save();
    }
}
