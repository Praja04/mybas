<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistSchedule extends Model
{
    public $table = 'checklist_schedule';
    protected $fillable = ['jenis_asset_id', 'year', 'month','week'];
    function jenis_asset()
    {
        return $this->belongsTo('App\JenisAsset', 'jenis_asset_id');
    }
    function checklists()
    {
        return $this->hasMany('App\TChecklistFlyCatcher', 'checklist_schedule_id');
    }
    function department()
    {
        return $this->belongsTo('App\Department','dept_id');
    }
}
