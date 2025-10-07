<?php

namespace App\Imports;

use App\Models\Klinik\Obat;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;


class KlinikImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function rules(): array
    {
        return [
            'nama_obat' => 'unique:klinik_obat',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_obat.unique' => ':attribute Sistem Mendeteksi Adanya NAMA OBAT Yang Double , Periksa Lagi Ya Excelnya..',
        ];
    }
    public function collection(Collection $collection)
    {
        foreach ($collection as $list) {
            $data = array(

                'nama_obat' => $list['nama_obat'],
                'harga' => $list['harga'],
                'satuan' => $list['satuan'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            Obat::insert($data);
        }
    }
}
