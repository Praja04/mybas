<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockBerasExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Tanggal' => $item->Tanggal,
                'Status' => $item->Status,
                'Jumlah Stock' => $item->jumlah_stock, 
                'Transaksi Masuk' => $item->transaksi_masuk, 
                'Transaksi Keluar' => $item->transaksi_keluar, 
                'Jumlah Stock Sesudah' => $item->jumlah_stock_sesudah, 
                'Keterangan' => $item->Keterangan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Status',
            'Jumlah Stock',
            'Transaksi Masuk',
            'Transaksi Keluar',
            'Jumlah Stock Sesudah',
            'Keterangan',
        ];
    }
}