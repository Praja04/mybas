<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPkNilai extends Model
{
    protected $table = 'hr_pk_nilai';

    protected $fillable = [
        'hr_pk_id',
        'hr_pk_detail_id',
        'nik',
        'nama',
        'nilai_self',
        'self_by',
        'self_at',
        'nilai_leader',
        'leader_by',
        'leader_at',
        'review',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'nilai_self' => 'integer',
        'nilai_leader' => 'integer',
        'review' => 'boolean',
        'self_at' => 'datetime',
        'leader_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];
}
