<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolParticipant extends Model
{
    protected $table = 'pool_participants';

    protected $fillable = [
        'rfid',
        'nik',
        'name',
        'department',
        'photo',
        'total_score',
        'booths_voted_count',
        'last_activity_at',
    ];

    protected $casts = [
        'total_score' => 'integer',
        'booths_voted_count' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function votes()
    {
        return $this->hasMany(PoolVote::class, 'participant_id');
    }

    public function votedBoothIds()
    {
        return $this->votes()->pluck('booth_id')->toArray();
    }

    public function canVoteMore($max = null)
    {
        if ($max === null) {
            $max = PoolBooth::where('is_active', true)->count();
            if ($max <= 0) {
                $max = PoolBooth::count();
            }
            $max = max(1, $max);
        }
        return $this->booths_voted_count < $max;
    }

    public function hasVotedBooth($boothId)
    {
        return $this->votes()->where('booth_id', $boothId)->exists();
    }
}
