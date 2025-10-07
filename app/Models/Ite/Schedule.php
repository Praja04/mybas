<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'name', 'project_id', 'plan_start_date', 'plan_end_date', 'actual_start_date', 'actual_end_date', 'status'
    ];
    public function project()
    {
    	return $this->belongsTo('App\Project', 'project_id');
    }
}
