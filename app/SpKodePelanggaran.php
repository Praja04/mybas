<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpKodePelanggaran extends Model
{
    protected $table = 'sp_kode_pelanggarans';

    protected $fillable = [
        'kode',
        'nama_pelanggaran',
        'jenis_sp',
        'pasal_dilanggar',
        'deskripsi',
    ];
}
