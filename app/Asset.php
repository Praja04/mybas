<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $primaryKey = 'id'; // or null

    public $incrementing = false;
    protected $fillable = [
    	'id','dept_id','jenis_asset_id','name','asset_number','location','keterangan', 'expire_time','status'	
    ];
    public function department()
    {
    	return $this->belongsTo('App\Department', 'dept_id');
    }
    public function jenis_asset()
    {
    	return $this->belongsTo('App\JenisAsset', 'jenis_asset_id');
    }
}
