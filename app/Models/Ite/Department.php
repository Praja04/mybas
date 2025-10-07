<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'name', 'status'
    ];

    public function gedung()
    {
    	return $this->belongsTo('App\Models\Ite\Gedung', 'gedung_id');
    }

    public function helpdesk_jobs()
    {
        return $this->hasMany('App\Models\Ite\HelpdeskJob', 'dept_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Ite\Department', 'dept_id');
    }

    public function cameras()
    {
        return $this->hasMany('App\Models\Ite\Camera', 'dept_id');
    }
}
