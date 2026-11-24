<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppForemanNilai extends Model
{
    protected $table = 'hr_ipp_foreman_nilai';

    protected $fillable = [
        'hr_ipp_foreman_id',
        'nik',
        'nama',
        'tahapan',
        'nilai',
        'filled_by',
        'filled_at',
        'approval_associate_manager',
        'approval_by_associate_manager',
        'approval_at_associate_manager',
        'note_associate_manager',
        'approval_manager',
        'approval_by_manager',
        'approval_at_manager',
        'note_manager',
    ];

    protected $casts = [
        'nilai' => 'integer',
        'filled_at' => 'datetime',
        'approval_at_associate_manager' => 'datetime',
        'approval_at_manager' => 'datetime',
    ];

    public function foreman()
    {
        return $this->belongsTo(HrIppForeman::class, 'hr_ipp_foreman_id');
    }
}
