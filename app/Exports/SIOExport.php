<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;

class SIOExport implements FromArray
{
    public function getSIOData(): array
    {
        $sioData = [];
        $sioData[] = ['No', 'Nama Perusahaan', 'Nama Perizinan', 'Nama Karyawan', 'NIK Karyawan', 'Departemen', 'Tanggal Mulai Ikatan Dinas', 'Tanggal Selesai Ikatan Dinas', 'Nomor Izin', 'Tanggal Terbit', 'Tanggal Habis', 'Harga', 'Keterangan'];

        $sios = SIO::with('department')
            ->where('status', '!=', 'deleted')
            ->get();

        foreach ($sios as $key => $sio) {
            $sioSertifikasi = SIOSertifikasi::where('id_sio', $sio->id)->where('status', '!=', 'deleted')->orderBy('tanggal_terbit', 'desc')->first();

            if ($sioSertifikasi) {
                $array = [
                    $key + 1,
                    $sio->perusahaan->nama_perusahaan,
                    $sio->nama_perizinan,
                    $sio->nama_karyawan,
                    $sio->nik_karyawan,
                    $sio->department->name,
                    $sio->tanggal_mulai_ikatan_dinas,
                    $sio->tanggal_selesai_ikatan_dinas,
                    $sioSertifikasi->nomor_izin,
                    $sioSertifikasi->tanggal_terbit,
                    $sioSertifikasi->tanggal_habis,
                    $sioSertifikasi->harga,
                    $sioSertifikasi->keterangan,
                ];
                $sioData[] = $array;
            }
        }
        return $sioData;
    }

    public function array(): array
    {
        return $this->getSIOData();
    }
}
