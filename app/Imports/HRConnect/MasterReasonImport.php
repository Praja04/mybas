<?php
namespace App\Imports\HRConnect;

use App\MasterReason; // Sesuaikan namespace model lu (App\Models\MasterReason atau App\MasterReason)
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MasterReasonImport implements ToModel, WithStartRow
{
    /**
     * Skip baris pertama (Header Excel)
     */
    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
    {
        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            return null;
        }

        return new MasterReason(
            [
                'tipe'        => $row[0],
                'kode_reason' => $row[1],
                'nama_reason' => $row[2],
                'is_active'   => 'Y',
            ]
        );
    }
}
