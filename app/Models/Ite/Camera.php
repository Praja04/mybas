<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
        'id',
    	'ip_address',
        'nvr_id',
        'name',
        'dept_id',
        'merk',
        'type',
        'channel_number',
        'model',
        'asset_number',
        'current_condition',
        'image',
        'username',
        'password',
        'location',
        'installed',
        'skip_check',
        'skip_reason'
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\Ite\Department', 'dept_id');
    }
}
