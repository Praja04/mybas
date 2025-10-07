<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'check_time',
    	'check_by',
    	'total_count',
    	'offline_count'
    ];

    public function cameras()
    {
    	return $this->belongsToMany('App\Models\Ite\Camera', 'checks_cameras', 'check_id', 'camera_id');
    }
}
