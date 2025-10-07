<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\MEN\MasterMaterial;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class MENMasterMaterialImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    protected $exclude;
    protected $upload_id;

    public function __construct($material_exclude, $upload_id)
    {
        $this->material_exclude = $material_exclude;
        $this->upload_id = $upload_id;
    }
    
    public function model(array $row)
    {
        if($row['plant'] == null || $row['plant'] == "\x00")
            return null;
        
        if(in_array($row['material_i'], $this->material_exclude))
            return null;

        if($this->decode($row['production']) != '' && $this->decode($row['expired_da']) !== '' ) {
            return $this->storeData($row);
        }
    }

    private function decode($string)
    {
        return str_replace("\x00", '', $string);
    }

    private function storeData($row)
    {
        $expired_date = strtotime($this->getDate($this->decode($row['expired_da'])));
        $production_date = strtotime($this->getDate($this->decode($row['production'])));
        $shelf_life = round(($expired_date-$production_date) / (60*60*24));

        return new MasterMaterial([
            'plant'                 => $this->decode($row['plant']),
            'sloc'                  => $this->decode($row['storage_lo']),
            'material'              => $this->decode($row['material_i']),
            'material_description'  => $this->decode($row['material_description']),
            'batch'                 => $this->decode($row['batch']),
            'uom'                   => $this->decode($row['base_unit']),
            'qty'                   => (int)$this->decode($row['unrestrict'])+(int)$this->decode($row['quality_in'])+(int)$this->decode($row['blocked']),
            'production_date'       => $this->getDate($this->decode($row['production'])),
            'expired_date'          => $this->getDate($this->decode($row['expired_da'])),
            'material_type'         => $this->decode($row['material_t']),
            'shelf_life'            => $shelf_life,
            'upload_id'             => $this->upload_id
        ]);
    }

    private function getDate($rawDate)
    {
        // dd($rawDate);
        // Ini berarti separator nya adalah "."
        if(count(explode('.', $rawDate)) > 1)
            return explode('.', $rawDate)[2].'-'.explode('.', $rawDate)[1].'-'.explode('.', $rawDate)[0];

            // Ini separator nya bukan titik. Kita anggap tanggal nya udah bener
        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate);
        return date_format($date, 'Y-m-d');
    }

    public function headingRow(): int
    {
        return 7;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => "\t"
        ];
    }
}
