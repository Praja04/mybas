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

        foreach ($rows as $row) 
        {
            $listItemsItemPeriksa = explode("\n", $row[1]);
            $formattedItemPeriksa = count($listItemsItemPeriksa) > 1 ? '<p>' . implode('</p><p>', $listItemsItemPeriksa) . '</p>' : $row[1];

            $listItems = explode("\n", $row[2]);
            $formattedKeterangan = count($listItems) > 1 ? '<p>' . implode('</p><p>', $listItems) . '</p>' : $row[2];

            MasterPertanyaan::create([
                'id_pertanyaan' => $this->createIdPertanyaan(),
                'id_group' => $this->id_group,
                'jenis' => $row[0],
                'item_periksa' =>  $formattedItemPeriksa,
                'keterangan' => $formattedKeterangan,
                'archive_status' => 'N',
            ]);
        }
    }

    protected function createIdPertanyaan()
    {
        // Get last record
        $lastRecord = MasterPertanyaan::latest('created_at')->latest('id_pertanyaan')->first();

        if($lastRecord == null) {
            $id_pertanyaan = 'Q001'; // Start with Q001 if no record exists
        } else {
            // Substring Q001 -> 001 and increment
            $id_pertanyaan = 'Q' . sprintf('%03d', intval(substr($lastRecord->id_pertanyaan, 1)) + 1);
        }

        return $id_pertanyaan;
    }
}
