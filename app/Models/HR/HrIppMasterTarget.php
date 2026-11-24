<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppMasterTarget extends Model
{
    protected $table = 'hr_ipp_master_target';

    protected $fillable = [
        'ipp_detail_id',
        'score',
        'match_type',
        'min_value',
        'max_value',
        'label',
    ];

    protected $casts = [
        'score' => 'integer',
        'min_value' => 'float',
        'max_value' => 'float',
    ];

    public function detail()
    {
        return $this->belongsTo(HrIppDetail::class, 'ipp_detail_id');
    }
}
