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
}
