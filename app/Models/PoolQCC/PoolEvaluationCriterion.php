<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolEvaluationCriterion extends Model
{
    protected $table = 'pool_evaluation_criteria';

    protected $fillable = [
        'name',
        'description',
        'weight',
        'max_score',
        'order',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'float',
        'max_score' => 'float',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function evaluationDetails()
    {
        return $this->hasMany(PoolJudgeEvaluationDetail::class, 'criterion_id');
    }
}

