<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class Operasional extends Model
{
    protected $table = 'sigra_operasional';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function sertifikasi()
    {
        return $this->hasMany(SertifikasiOperasional::class, 'id_operasional')->where('status', '!=', 'deleted');
    }
}
