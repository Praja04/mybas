<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolJudgeEvaluation extends Model
{
    protected $table = 'pool_judge_evaluations';

    protected $fillable = [
        'judge_id',
        'booth_id',
        'score',
        'notes',
        'evaluated_at',
    ];

    protected $casts = [
        'score' => 'float',
        'evaluated_at' => 'datetime',
    ];

    public function judge()
    {
        return $this->belongsTo(PoolJudge::class, 'judge_id');
    }

    public function booth()
    {
        return $this->belongsTo(PoolBooth::class, 'booth_id');
    }
}

