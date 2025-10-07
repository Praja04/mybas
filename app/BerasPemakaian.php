<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BerasPemakaian extends Model
{
    protected $table = 'beras_pemakaians';
    protected $guarded = [];

    public function penguranganPemakaian()
    {
        return $this->belongsTo(BerasPengambilan::class, 'id_stock', 'id');
    }
    public function KedatanganPesananBeras()
    {
        return $this->belongsTo(BerasStock::class, 'id_stock', 'id_stock');
    }
}
