<?php

namespace App\Models\Klinik;

use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    protected $table = 'klinik_pemeriksaan';
    protected $guarded = [];

    public function obat()
    {
        return $this->belongsToMany('App\Models\Klinik\Obat', 'klinik_pemeriksaan_obat', 'id_pemeriksaan', 'id_obat')->withPivot('quantity', 'harga');
    }

    public function data_diagnosa()
    {
        return $this->belongsTo('App\Models\Klinik\Diagnosa', 'diagnosa');
    }
}
