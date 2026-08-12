<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpKodePelanggaran extends Model
{
    protected $table = 'sp_kode_pelanggarans';

    protected $fillable = [
        'kategori_kode',
        'kode',
        'nama_pelanggaran',
        'bentuk_pelanggaran',
        'dasar_pertimbangan',
        'jenis_sp',
        'pasal_dilanggar',
        'deskripsi',
    ];
}
