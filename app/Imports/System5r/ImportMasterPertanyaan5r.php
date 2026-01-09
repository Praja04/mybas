<?php

namespace App\Imports\System5r;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\System5R\MasterPertanyaan;

class ImportMasterPertanyaan5r implements ToCollection
{
    protected $id_group;
    protected $id_pertanyaan;

    public function __construct($id_group, $id_pertanyaan)
    {
        $this->id_group = $id_group;
        $this->id_pertanyaan = $id_pertanyaan;
    }

    public function collection(Collection $rows)
    {
        // Remove the first row which is the header row
        $rows->shift();

        // Get starting ID number once before the loop
        $lastRecord = MasterPertanyaan::orderByRaw('CAST(SUBSTRING(id_pertanyaan, 2) AS SIGNED) DESC')->first();
        
        if($lastRecord == null) {
            $currentIdNumber = 1;
        } else {
            $currentIdNumber = intval(substr($lastRecord->id_pertanyaan, 1)) + 1;
        }

        // Process each row sequentially
        foreach ($rows as $row) 
        {
            // Skip empty rows
            if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                continue;
            }

            // Format item_periksa
            $listItemsItemPeriksa = explode("\n", $row[1]);
            $formattedItemPeriksa = count($listItemsItemPeriksa) > 1 
                ? '<p>' . implode('</p><p>', $listItemsItemPeriksa) . '</p>' 
                : $row[1];

            // Format keterangan
            $listItems = explode("\n", $row[2]);
            $formattedKeterangan = count($listItems) > 1 
                ? '<p>' . implode('</p><p>', $listItems) . '</p>' 
                : $row[2];

            // Create with sequential ID
            MasterPertanyaan::create([
                'id_pertanyaan' => 'Q' . str_pad($currentIdNumber, 3, '0', STR_PAD_LEFT),
                'id_group' => $this->id_group,
                'jenis' => $row[0],
                'item_periksa' => $formattedItemPeriksa,
                'keterangan' => $formattedKeterangan,
                'archive_status' => 'N',
            ]);

            // Increment for next row
            $currentIdNumber++;
        }
    }
}