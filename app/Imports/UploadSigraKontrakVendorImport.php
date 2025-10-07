<?php

namespace App\Imports;

use App\Models\Sigra\KontrakVendor;
use App\Models\Sigra\MasterVendor;
use App\Models\Sigra\Perusahaan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UploadSigraKontrakVendorImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            perusahaan::create([
                'nama_perusahaan' => $row[0],
            ]);

            MasterVendor::create([
                'nama_vendor' => $row[1],
                'jenis_pekerjaan' => $row[2],
            ]);

            KontrakVendor::create([
                'tanggal_mulai' => $row[3],
                'tanggal_selesai' => $row[4],
                'value' => $row[5],
                'keterangan' => $row[6],
            ]);
        }
    }
}
