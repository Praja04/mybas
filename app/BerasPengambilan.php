<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BerasPengambilan extends Model
{
    protected $table = 'beras_pengambilans';
    protected $guarded = [];

    public function pengambilanStock()
    {
        return $this->belongsTo(BerasJumlah::class, 'id_stock', 'id_stock');
    }
    public function pemakaianBeras()
    {
        return $this->hasMany(BerasPemakaian::class, 'id_stock', 'id_stock');
    }
}
