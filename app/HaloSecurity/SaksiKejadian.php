<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class SaksiKejadian extends Model
{
    protected $table = "saksi_kejadian"; // menghubungkan ke dalam tabel saksi_kejadian

    protected $guarded = [];
    
    public function balaporankejadians()
    {
        $this->belongsTo('App\HaloSecurity\BaLaporanKejadian');
    }
}
