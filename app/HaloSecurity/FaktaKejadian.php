<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class FaktaKejadian extends Model
{
    protected $table = "fakta_kejadian"; // menghubungkan ke dalam tabel fakta_kejadian

    protected $guarded = [];

    public function balaporankejadians()
    {
        $this->belongsTo('App\HaloSecurity\BaLaporanKejadian');
    }
}
