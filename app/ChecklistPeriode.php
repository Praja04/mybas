<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistPeriode extends Model
{
    public $table = 'checklist_periode';
    protected $fillable = ['jenis_asset_id', 'periode', 'day_no'];
    function jenis_asset()
    {
        return $this->belongsTo('App\JenisAsset', 'jenis_asset_id');
    }
}
