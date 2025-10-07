<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BerasJumlah extends Model
{
    protected $table = 'jumlah_beras';
    protected $guarded = [];

    public function kedatanganBeras()
    {
        return $this->belongsTo(BerasStock::class, 'id_stock', 'id_stock');
    }

    public function pengambilanBeras()
    {
        return $this->hasMany(BerasPengambilan::class, 'id_stock', 'id_stock');
    }

    public function penggunaanBeras()
    {
        return $this->hasMany(BerasPemakaian::class, 'id_stock', 'id_stock');
    }
}
