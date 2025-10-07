<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Sigra\Perusahaan;
use App\Models\Sigra\SIO;
use App\Exports\SIOExport;
use App\Models\Sigra\SIOSertifikasi;
use App\Models\LocalAttachment;
use Illuminate\Support\Facades\Session;
use Auth;

class UploadSigraSioimport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
    }
}
