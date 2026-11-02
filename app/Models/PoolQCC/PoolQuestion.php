<?php

namespace App\Models\PoolQCC;

use Illuminate\Database\Eloquent\Model;

class PoolQuestion extends Model
{
    protected $table = 'pool_questions';

    protected $fillable = [
        'booth_id',
        'question_text',
        'image',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_answer',
        'points',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    public function booth()
    {
        return $this->belongsTo(PoolBooth::class, 'booth_id');
    }

    public function votes()
    {
        return $this->hasMany(PoolVote::class, 'question_id');
    }
}
