<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SHBahanBaku extends Model
{
    protected $table = 'sigra_sh_bahan_baku';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function sertifikat()
    {
        return $this->hasMany(SertifikatSHBahanBaku::class, 'id_bahan')->where('status', '!=', 'deleted');
    }
}
