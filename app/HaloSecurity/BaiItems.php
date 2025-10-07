<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class BaiItems extends Model
{
    protected $table = "bai_items"; // menghubungkan ke dalam tabel bai_items

    protected $guarded = [];

    public function baintrogasis()
    {
        $this->belongsTo('App\HaloSecurity\BaIntrogasi');
    }
}
