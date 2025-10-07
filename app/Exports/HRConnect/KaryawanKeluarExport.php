<?php

namespace App\Exports\HRConnect;

use App\HrKaryawan;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class KaryawanKeluarExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $karyawanCollection = new Collection();

        foreach ($this->data as $item) {
            $karyawan = HrKaryawan::where('id', $item['id'])
                                    ->first();

            if($karyawan) {
                $karyawanCollection->push([
                    'nama' => $item['nama'],
                    'nik' => $item['nik'],
                    'kode_divisi' => $item['kode_divisi'],
                    'kode_bagian' => $item['kode_bagian'],
                    'kode_admin' => $item['kode_admin'],
                    'alasan_keluar' => $item['alasan_keluar'],
                    'tanggal_keluar' => $item['tanggal_keluar'],
                ]);
            }
        }

        return view('hr-connect.exports.karyawan_keluar', [
            'karyawanCollection' => $karyawanCollection
        ]);
    }
}
