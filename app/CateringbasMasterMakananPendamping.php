<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CateringbasMasterMakananPendamping extends Model
{
    protected $table = 'master_makanan_pendamping_cateringbas';
    protected $guarded = [];

    public function jumlahPesanan()
    {
        return $this->hasMany(CateringbasPengecekanJumlahPesanan::class, 'id', 'id_pesanan');
    }
}
