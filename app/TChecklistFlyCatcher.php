<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TChecklistFlyCatcher extends Model
{
    public $table = 't_checklist_fly_catcher';
    protected $fillable = ['asset_id','checklist_schedule_id','kondisi','keterangan','check_time','check_by','pictures','pest_count'];

    public function asset()
    {
    	return $this->belongsTo('App\Asset', 'asset_id');
    }
    public function schedule()
    {
        return $this->belongsTo('App\ChecklistSchedule', 'checklist_schedule_id');
    }
}
