<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CateringbasMasterMakananUtama extends Model
{
    protected $table = 'master_makanan_utama_cateringbas';
    protected $guarded = [];

    public function jumlahPesananPendamping()
    {
        return $this->hasMany(CateringbasPengecekanJumlahPesanan::class, 'id', 'id_pesanan');
    }
}
