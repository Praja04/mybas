<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TChecklistKaca extends Model
{
    public $table = 't_checklist_kaca';
    protected $fillable = ['asset_id','kondisi','keterangan','check_time','check_by','pictures'];

    public function asset()
    {
    	return $this->belongsTo('App\Asset', 'asset_id');
    }
}
