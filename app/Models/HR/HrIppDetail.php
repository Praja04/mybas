<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppDetail extends Model
{
    protected $table = 'hr_ipp_detail';

    protected $fillable = [
        'ipp_id',
        'no_urut',
        'objective',
        'kpa',
        'kpi',
        'classification',
        'uom',
        'weight',
        'target',
        'due_date',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function ipp()
    {
        return $this->belongsTo(HrIpp::class, 'ipp_id');
    }

    public function masterTargets()
    {
        return $this->hasMany(HrIppMasterTarget::class, 'ipp_detail_id');
    }
}