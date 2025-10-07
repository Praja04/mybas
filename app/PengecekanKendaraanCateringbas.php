<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengecekanKendaraanCateringbas extends Model
{
    protected $table = 'pengecekan_kendaraan_cateringbas';
    protected $guarded = [];

    public function pengirimCateringbas()
    {
        return $this->belongsTo(PengirimCateringbas::class, 'id_transaksi', 'id');
    }
}
