<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolVote extends Model
{
    protected $table = 'pool_votes';

    protected $fillable = [
        'participant_id',
        'booth_id',
        'question_id',
        'participant_answer',
        'is_correct',
        'points_awarded',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_awarded' => 'integer',
        'answered_at' => 'datetime',
    ];

    public function participant()
    {
        return $this->belongsTo(PoolParticipant::class, 'participant_id');
    }

    public function booth()
    {
        return $this->belongsTo(PoolBooth::class, 'booth_id');
    }

    public function question()
    {
        return $this->belongsTo(PoolQuestion::class, 'question_id');
    }
}
