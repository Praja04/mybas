<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BufferStockBerasExport implements FromCollection, WithHeadings
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
                'Jumlah Pemakaian' => $item->jumlah_pengambilan_sebelum, 
                'Transaksi Masuk' => $item->transaksi_masuk, 
                'Transaksi Keluar' => $item->transaksi_keluar, 
                'Jumlah Pemakaian Sesudah' => $item->jumlah_pengambilan_sesudah, 
                'Keterangan' => $item->Keterangan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Status',
            'Jumlah Pemakaian',
            'Transaksi Masuk',
            'Transaksi Keluar',
            'Jumlah Pemakaian Sesudah',
            'Keterangan',
        ];
    }
}
