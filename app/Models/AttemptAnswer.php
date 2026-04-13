<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model {
    protected $fillable = [
        'attempt_id', 
        'question_id', 
        'option_id',
        'text_answer',
        'is_correct',
        'earned_marks'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'earned_marks' => 'float',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id', 'id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'option_id', 'id');
    }
}