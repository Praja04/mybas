<?php

namespace App\Exports;

use App\ecafeSedaapBas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class PesananExport implements WithMultipleSheets
{
    use Exportable;

    private $startDate;
    private $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        $data = ecafeSedaapBas::select('tanggal', 'kategori', 'shift', 'jumlah')
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->get();

        $groupedData = $data->groupBy('kategori');

        if ($groupedData->isEmpty()) {
            return $sheets;
        }

        foreach ($groupedData as $category => $data) {
            if ($data->isNotEmpty()) {
                $sheets[] = new CategorySheet($data, $category);
            }
        }

        return $sheets;
    }
}

// Kelas untuk setiap sheet berdasarkan kategori
class CategorySheet implements FromCollection, WithHeadings, WithTitle
{
    private $data;
    private $category;

    public function __construct(Collection $data, $category)
    {
        $this->data = $data;
        $this->category = $category;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kategori', 'Shift', 'Jumlah'];
    }

    // Atur judul sheet sesuai dengan kategori
    public function title(): string
    {
        return str_replace(['\\', '/', '*', '?', ':', '[', ']'], '', $this->category);
    }
}
