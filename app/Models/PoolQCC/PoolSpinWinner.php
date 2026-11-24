<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolSpinWinner extends Model
{
    protected $table = 'pool_spin_winners';

    protected $fillable = [
        'tier_key',
        'participant_id',
        'name',
        'nik',
        'department',
        'employee_type',
        'is_non_staff',
        'weight',
        'total_score',
        'booths_voted_count',
        'order',
        'won_at',
    ];

    protected $casts = [
        'is_non_staff' => 'boolean',
        'weight' => 'integer',
        'total_score' => 'integer',
        'booths_voted_count' => 'integer',
        'order' => 'integer',
    ];

    public function participant()
    {
        return $this->belongsTo(PoolParticipant::class, 'participant_id');
    }
}
