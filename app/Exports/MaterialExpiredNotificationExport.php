<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class MaterialExpiredNotificationExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($materialas)
    {
        $this->materials = $materialas;
    }

    public function collection()
    {
        return $this->materials;
    }
}
