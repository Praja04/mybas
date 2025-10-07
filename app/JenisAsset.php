<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisAsset extends Model
{
    protected $fillable = [
    	'name','slug'
    ];

    public function periode()
    {
    	return $this->belongsTo('App\ChecklistPeriode', 'id');
    }
}
