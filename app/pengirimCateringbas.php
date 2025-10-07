<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengirimCateringbas extends Model
{
    protected $table = 'pengirim_cateringbas';
    protected $guarded = [];

    public function pengecekanKendaraan()
    {
        return $this->hasOne(PengecekanKendaraanCateringbas::class, 'id_transaksi', 'id');
    }

    public function pengecekanJumlahPesanan()
    {
        return $this->hasMany(CateringbasPengecekanJumlahPesanan::class, 'id_transaksi', 'id');
    }

    public function pengambilanSamplePesanan()
    {
        return $this->hasMany(pengambilanSampelCateringbas::class, 'id_transaksi', 'id');
    }
}
