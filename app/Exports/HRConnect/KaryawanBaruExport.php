<?php

namespace App\Exports\HRConnect;

use App\HrKaryawan;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KaryawanBaruExport implements FromView
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
            $karyawan = HrKaryawan::where('id', $item['idCheckwish'])
                                    ->first();

            if($karyawan) {
                $karyawanCollection->push([
                    'nama' => $karyawan->nama,
                    'nik' => $karyawan->nik,
                    'tanggal_masuk' => $karyawan->tanggal_masuk,
                    'jenis_kelamin' => $karyawan->jenis_kelamin,
                    'kode_divisi' => $karyawan->kode_divisi,
                    'kode_bagian' => $karyawan->kode_bagian,
                    'kode_group' => $karyawan->kode_group,
                    'p_in' => $karyawan->p_in == 'Y' ? 'IN' : 'NO-IN' 
                ]);
            }
        }

        return view('hr-connect.exports.karyawan_baru', [
            'karyawanCollection' => $karyawanCollection
        ]);
    }
}
