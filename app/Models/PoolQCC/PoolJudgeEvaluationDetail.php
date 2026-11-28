<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolJudgeEvaluationDetail extends Model
{
    protected $table = 'pool_judge_evaluation_details';

    protected $fillable = [
        'evaluation_id',
        'criterion_id',
        'score',
        'weight',
        'notes',
    ];

    protected $casts = [
        'score' => 'float',
        'weight' => 'float',
    ];

    public function evaluation()
    {
        return $this->belongsTo(PoolJudgeEvaluation::class, 'evaluation_id');
    }

    public function criterion()
    {
        return $this->belongsTo(PoolEvaluationCriterion::class, 'criterion_id');
    }
}

