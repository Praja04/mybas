<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class ProjectJob extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'project_id','name','date','start_time','end_time','pictures','description','progress','status'
    ];
    public function project()
    {
    	return $this->belongsTo('App\Project', 'project_id');
    }
}
