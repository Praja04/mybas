<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppAktual extends Model
{
    protected $table = 'hr_ipp_aktual';

    protected $fillable = [
        'ipp_otorisasi_id',
        'ipp_detail_id',
        'tahapan',
        'aktual',
        'aktual_varchar',
        'aktual_atasan',
        'aktual_atasan_varchar',
        'aktual_atasan_by',
        'aktual_departemen',
        'aktual_departemen_varchar',
        'aktual_departemen_by',
        'achievement_category',
        'achievement_score',
        'score_scale',
        'review',
        'reviewed_by',
        'reviewed_at',
        'keterangan',
        'send_by_username',
        'approval_associate_manager',
        'approval_by_associate_manager',
        'approval_at_associate_manager',
        'approval_manager',
        'approval_by_manager',
        'approval_at_manager',
    ];

    protected $casts = [
        'aktual' => 'float',
        'aktual_atasan' => 'float',
        'aktual_departemen' => 'float',
        'achievement_score' => 'float',
        'score_scale' => 'float',
        'review' => 'boolean',
        'reviewed_at' => 'datetime',
        'approval_at_associate_manager' => 'datetime',
        'approval_at_manager' => 'datetime',
    ];

    public function otorisasi()
    {
        return $this->belongsTo(HrIppOtorisasi::class, 'ipp_otorisasi_id');
    }

    public function detail()
    {
        return $this->belongsTo(HrIppDetail::class, 'ipp_detail_id');
    }
}
