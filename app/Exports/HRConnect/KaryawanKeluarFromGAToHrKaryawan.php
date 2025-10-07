<?php

namespace App\Exports\HRConnect;

use App\HrKaryawan;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KaryawanKeluarFromGAToHrKaryawan implements FromView
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
            $karyawan = HrKaryawan::where('id', $item['checklistId'])
                                    ->first();

            if($karyawan) {
                $karyawanCollection->push([
                    'nama' => $karyawan->nama,
                    'nik' => $karyawan->nik,
                    'alasan_keluar' => $karyawan->alasan_keluar,
                    'tanggal_keluar' => $karyawan->tanggal_keluar,
                ]);
            }
        }

        return view('hr-connect.exports.karyawan_keluar', [
            'karyawanCollection' => $karyawanCollection
        ]);
    }
}
