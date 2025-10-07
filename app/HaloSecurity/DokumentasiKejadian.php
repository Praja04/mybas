<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class DokumentasiKejadian extends Model
{
    protected $table = "dokumentasi_kejadian"; // menghubungkan ke dalam tabel dokumentasi_kejadian

    protected $guarded = [];

    public function balaporankejadians()
    {
        $this->belongsTo('App\HaloSecurity\BaLaporanKejadian');
    }
}
