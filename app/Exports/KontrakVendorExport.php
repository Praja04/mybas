<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Sigra\KontrakVendor;

class KontrakVendorExport implements FromArray
{
    public function getKontrak(): array
    {
        $kontrakVendorData = [];
        $kontrakVendorData[] = ['no', 'perusahaan', 'jenis_pekerjaan', 'tanggal_mulai', 'tanggal_selesai', 'value', 'keterangan'];

        $kontrakVendors = KontrakVendor::with('perusahaan')->where('status', '!=', 'deleted')->get();

        foreach ($kontrakVendors as $key => $data) {
            // dd($data->perusahaan->nama_perusahaan);
            $namaPerusahaan = $data->perusahaan->nama_perusahaan ?? '';
            $jenisPekerjaan = $data->vendor->jenis_pekerjaan ?? '';
            $tanggalMulai = $data->tanggal_mulai ?? '';
            $tanggalSelesai = $data->tanggal_selesai ?? '';
            $value = number_format($data->value, 0, ',', '.');
            $keterangan = $data->keterangan ?? '';

            $array = [
                $key + 1,
                $namaPerusahaan,
                $jenisPekerjaan,
                $tanggalMulai,
                $tanggalSelesai,
                $value,
                $keterangan,
            ];

            $kontrakVendorData[] = $array;
        }

        return $kontrakVendorData;
    }

    public function array(): array
    {
        return $this->getKontrak();
    }
}
