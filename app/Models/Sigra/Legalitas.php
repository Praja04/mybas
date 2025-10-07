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
}
