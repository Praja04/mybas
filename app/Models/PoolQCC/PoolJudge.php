<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolJudge extends Model
{
    protected $table = 'pool_judges';

    protected $fillable = [
        'rfid',
        'nik',
        'name',
        'department',
        'title',
        'vote_weight',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'vote_weight' => 'integer',
    ];

    public function evaluations()
    {
        return $this->hasMany(PoolJudgeEvaluation::class, 'judge_id');
    }

    public function evaluatedBoothIds()
    {
        return $this->evaluations()->pluck('booth_id')->toArray();
    }

    public function evaluationForBooth($boothId)
    {
        return $this->evaluations()->where('booth_id', $boothId)->first();
    }
}

