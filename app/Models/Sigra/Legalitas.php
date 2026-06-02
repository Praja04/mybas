<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class Legalitas extends Model
{
    protected $table = 'sigra_legalitas';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function sertifikasi()
    {
        return $this->hasMany(SertifikasiLegalitas::class, 'id_legalitas')->where('status', '!=', 'deleted');
    }
}
