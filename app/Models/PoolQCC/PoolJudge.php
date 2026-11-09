<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolJudge extends Model
{
    protected $table = 'pool_judges';

    protected $fillable = [
        'user_id',
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
        'user_id' => 'integer',
        'is_active' => 'boolean',
        'vote_weight' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

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

