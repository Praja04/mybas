<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PKW extends Model
{
    protected $table = 'pkw';
    protected $guarded = [];

    public function karyawan()
    {
    	return $this->belongsTo('App\PKWKaryawan', 'id_karyawan');
    }

    public function form_pa()
    {
    	return $this->hasOne('App\PKWFormPA', 'id_pkw');
    }
}
