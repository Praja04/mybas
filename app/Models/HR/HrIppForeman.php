<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppForeman extends Model
{
    protected $table = 'hr_ipp_foreman';

    protected $fillable = [
        'ipp_id',
        'no_urut',
        'section',
        'area',
        'competency_behavior',
        'key_behavior',
        'kriteria_1',
        'kriteria_2',
        'kriteria_3',
        'kriteria_4',
        'kriteria_5',
    ];

    public function ipp()
    {
        return $this->belongsTo(HrIpp::class, 'ipp_id');
    }

    public function nilai()
    {
        return $this->hasMany(HrIppForemanNilai::class, 'hr_ipp_foreman_id');
    }
}
