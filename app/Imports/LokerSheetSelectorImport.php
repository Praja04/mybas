<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LokerSheetSelectorImport implements WithMultipleSheets
{
    public $sheetIndex;
    public $importInstance;

    public function __construct($sheetIndex, $importInstance)
    {
        $this->sheetIndex     = $sheetIndex;
        $this->importInstance = $importInstance;
    }

    public function sheets(): array
    {
        return [
            $this->sheetIndex => $this->importInstance,
        ];
    }
}
