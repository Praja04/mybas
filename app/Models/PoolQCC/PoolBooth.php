<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolBooth extends Model
{
    protected $table = 'pool_booths';

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'location',
        'pic_name',
        'pic_contact',
        'image',
        'total_points',
        'total_voters',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_points' => 'integer',
        'total_voters' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(PoolQuestion::class, 'booth_id');
    }

    public function activeQuestions()
    {
        return $this->hasMany(PoolQuestion::class, 'booth_id')->where('is_active', true);
    }

    public function votes()
    {
        return $this->hasMany(PoolVote::class, 'booth_id');
    }

    public function judgeEvaluations()
    {
        return $this->hasMany(PoolJudgeEvaluation::class, 'booth_id');
    }

    // Helper untuk mengecek apakah pertanyaan sudah mencapai minimal 5
    public function hasMinQuestions($min = 5)
    {
        return $this->activeQuestions()->count() >= $min;
    }
}
