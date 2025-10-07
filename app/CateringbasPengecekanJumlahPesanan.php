<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CateringbasPengecekanJumlahPesanan extends Model
{
    protected $table = 'cateringbas_pengecekan_jumlah_pesanan';
    protected $guarded = [];

    public function pengirimCateringbas()
    {
        return $this->belongsTo(PengirimCateringbas::class, 'id_transaksi', 'id');
    }

    public function masterMenuUtama()
    {
        return $this->belongsTo(CateringbasMasterMakananUtama::class, 'id_pesanan', 'id');
    }

    public function masterMenuPendamping()
    {
        return $this->belongsTo(CateringbasMasterMakananPendamping::class, 'id_pesanan', 'id');
    }
}
