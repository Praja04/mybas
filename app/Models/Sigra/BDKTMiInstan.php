<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class BDKTMiInstan extends Model
{
    protected $table = 'sigra_bdkt_mi_instan';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }
}
