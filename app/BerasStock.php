<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BerasStock extends Model
{
    protected $table = 'kedatangan_beras_stocks';
    protected $guarded = [];
    protected $dates = ['tanggal'];


    public function stock()
    {
        return $this->hasMany(BerasJumlah::class, 'id_stock', 'id_stock');
    }

    public function pengirimanStock()
    {
        return $this->hasMany(BerasPengambilan::class, 'id_stock', 'id_stock');
    }

    public function penguranganPengambilan()
    {
        return $this->hasMany(BerasPemakaian::class, 'id_stock', 'id_stock');
    }
}
