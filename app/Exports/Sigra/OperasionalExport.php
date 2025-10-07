<?php

namespace App\Exports\Sigra;

use Maatwebsite\Excel\Concerns\FromArray;
use App\Models\Sigra\Operasional;
use App\Models\Sigra\SertifikasiOperasional;

class OperasionalExport implements FromArray
{

    private function getOperasional()
    {
        $sertifikasi_operasional = [];
        $sertifikasi_operasional[] = ['no', 'perusahaan', 'nama_perizinan', 'nomor_perizinan', 'tanggal_sertifikasi', 'tanggal_expired', 'remarks', 'tahun'];

        $operasional = Operasional::where('status', '!=', 'deleted')->get();

        foreach ($operasional as $key => $data) {
            $sertifikasi = SertifikasiOperasional::where('id_operasional', $data->id)->where('status', '!=', 'deleted')->orderBy('tahun', 'desc')->first();

            if ($sertifikasi != null) {
                $array = [
                    $key + 1,
                    $data->perusahaan->nama_perusahaan,
                    $data->nama_perizinan,
                    $data->nomor_perizinan,
                    $sertifikasi->tanggal_sertifikasi,
                    $sertifikasi->tanggal_expired,
                    $sertifikasi->remarks,
                    $sertifikasi->tahun,
                ];
                $sertifikasi_operasional[] = $array;
            }
        }
        return $sertifikasi_operasional;
    }

    public function array(): array
    {
        return $this->getOperasional();
    }
}
