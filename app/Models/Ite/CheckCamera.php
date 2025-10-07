<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class CheckCamera extends Model
{
    protected $connection = 'ite';
    protected $table = 'checks_cameras';
    protected $fillable = [
    	'check_id',
    	'camera_id'
    ];

    public function camera()
    {
    	return $this->belongsTo('App\Models\Ite\Camera', 'camera_id');
    }

    public function cameras()
    {
    	return $this->hasMany('App\Models\Ite\Camera', 'id');
    }
}
