<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonitoringScanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $kategori;
    protected $shift;
    protected $departemen;
    protected $index = 0;

    public function __construct($startDate, $endDate, $kategori, $shift = 'semua', $departemen = 'semua')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kategori = $kategori;
        $this->shift = $shift;
        $this->departemen = $departemen;
    }

    public function collection()
    {
        $query = DB::table('ecafesedaap_scan')
            ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

        if ($this->kategori !== 'semua') {
            $query->where('kategori', $this->kategori);
        }

        if ($this->shift !== 'semua') {
            $query->where('shift', $this->shift);
        }

        if ($this->departemen !== 'semua') {
            $query->where('departemen', $this->departemen);
        }

        $data = $query->orderBy('waktu', 'desc')->get();

        // Populate missing departments (Legacy support)
        $niksMissingDept = [];
        foreach ($data as $item) {
            if (empty($item->departemen)) {
                $niksMissingDept[] = $item->nik;
            }
        }

        if (!empty($niksMissingDept)) {
            try {
                $deptMap = DB::connection('192.168.178.44-admin')
                    ->table('MSIDCARD')
                    ->whereIn('NIK', array_unique($niksMissingDept))
                    ->pluck('DEPTID', 'NIK');

                foreach ($data as $item) {
                    if (empty($item->departemen)) {
                        $item->departemen = $deptMap[$item->nik] ?? '-';
                    }
                }
            } catch (\Exception $e) {
                // Silently fail or log if needed
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIK',
            'Departemen',
            'Kategori',
            'Shift',
            'Tanggal Scan',
            'Waktu Scan',
        ];
    }

    public function map($item): array
    {
        $this->index++;
        return [
            $this->index,
            $item->nama,
            $item->nik,
            $item->departemen ?? '-',
            $item->kategori,
            'Shift ' . $item->shift,
            $item->tanggal,
            $item->waktu,
        ];
    }
}
