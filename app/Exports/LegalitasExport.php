<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Sigra\Legalitas;
use App\Models\Sigra\SertifikasiLegalitas;


class LegalitasExport implements FromArray
{
    public function getLegalitas(): array
    {
        $SigraLegalitas = [];
        $SigraLegalitas[] = ['No', 'Nama Dokumen', 'Nomor Dokumen', 'Instansi', 'Terbit', 'Expired', 'Berlaku', 'Remarks'];

        $operasionalLegalitas = Legalitas::with(['perusahaan', 'sertifikasi'])->where('status', '!=', 'deleted')->get();

        foreach ($operasionalLegalitas as $key => $data) {
            // dd($data->perusahaan->nama_perusahaan);
            $sertifikasiLegalitas = $data->sertifikasi->sortByDesc('tanggal_terbit')->first();

            if ($sertifikasiLegalitas) {
                $array = [
                    $key + 1,
                    $data->perusahaan->nama_perusahaan,
                    $data->nama_legalitas,
                    $sertifikasiLegalitas->nomor_dokumen,
                    $sertifikasiLegalitas->instansi,
                    $sertifikasiLegalitas->tanggal_terbit,
                    $sertifikasiLegalitas->tanggal_habis,
                    $sertifikasiLegalitas->masa_berlaku,
                    $sertifikasiLegalitas->keterangan,

                ];
                $SigraLegalitas[] = $array;
            }
        }
        return $SigraLegalitas;
    }

    public function array(): array
    {
        return $this->getLegalitas();
    }
}
