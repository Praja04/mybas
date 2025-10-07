<?php

namespace App\Models\Klinik;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'klinik_obat';
    protected $guarded = [];

    public function pemeriksaan()
    {
        return $this->belongsToMany('App\Models\Klinik\Pemeriksaan', 'klinik_pemeriksaan_obat', 'id_obat', 'id_pemeriksaan')->withPivot('quantity', 'harga');
        // return $this->hasMany('App\Models\Klinik\PemeriksaanObat', 'id_obat');
    }

    
}
