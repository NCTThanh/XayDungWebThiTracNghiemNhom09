<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'attempt_id',
        'score',
        'is_passed',
        'teacher_feedback'
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    // ==================== ATTRIBUTES ====================

    // Kiểm tra đã pass chưa
    public function isPassed(): bool
    {
        return $this->is_passed === 1 || $this->score >= 5; // Giả sử thang điểm 10, pass >= 5
    }

    // Format điểm số
    public function getScoreFormattedAttribute(): string
    {
        return number_format($this->score, 2);
    }
}