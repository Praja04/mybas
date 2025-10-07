<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pengambilanSampelCateringbas extends Model
{
    protected $table = 'pengambilan_sampel_cateringbas';
    protected $guarded = [];

    public function PengirimCateringSampel()
    {
        return $this->belongsTo(PengirimCateringbas::class, 'id_transaksi', 'id');
    }
}
