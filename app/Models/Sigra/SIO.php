<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SIO extends Model
{
    protected $table = 'sigra_sio';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }
}
