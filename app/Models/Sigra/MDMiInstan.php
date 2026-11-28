<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class MDMiInstan extends Model
{
    protected $table = 'sigra_md_mi_instan';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function sertifikat()
    {
        return $this->hasMany(SertifikatMDMiInstan::class, 'id_varian')->where('status', '!=', 'deleted');
    }
}
