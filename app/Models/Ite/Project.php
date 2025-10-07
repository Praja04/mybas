<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'name', 'start_date', 'target_date', 'dept_id', 'pic', 'priority', 'status'
    ];
    public function department()
    {
    	return $this->belongsTo('App\Models\Ite\Department', 'dept_id');
    }
    public function order()
    {
    	return $this->hasOne('App\Models\Ite\Order', 'project_id');
    }
    public function jobs()
    {
        return $this->hasMany('App\Models\Ite\ProjectJob', 'project_id');
    }
    public function schedules()
    {
        return $this->hasMany('App\Models\Ite\Schedule', 'project_id');
    }
}
