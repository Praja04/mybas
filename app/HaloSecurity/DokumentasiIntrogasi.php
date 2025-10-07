<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class DokumentasiIntrogasi extends Model
{
    protected $table = "dokumentasi_introgasi"; // menghubungkan ke dalam tabel dokumentasi_introgasi

    protected $guarded = [];

    public function baintrogasis()
    {
        $this->belongsTo('App\HaloSecurity\BaIntrogasi');
    }
}
