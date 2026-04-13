<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'user_id', 
        'quiz_id', 
        'start_time', 
        'end_time',
        'score',
        'status', 
        'cheat_warnings',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'float',
        'cheat_warnings' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id', 'id');
    }

    /**
     * Tính toán thời gian hoàn thành
     */
    public function getCompletionTimeAttribute(): ?int
    {
        if ($this->start_time && $this->end_time) {
            return $this->end_time->diffInSeconds($this->start_time);
        }
        return null;
    }

    /**
     * Format thời gian (HH:MM:SS)
     */
    public function getFormattedTimeAttribute(): string
    {
        $seconds = $this->completion_time;
        if ($seconds === null) return '0:00:00';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Kiểm tra xem người dùng đã pass hay không
     */
    public function isPassed(): bool
    {
        $passScore = $this->quiz->pass_score ?? 5.0;
        return $this->score >= $passScore;
    }

    /**
     * Lấy tổng số câu hỏi
     */
    public function getTotalQuestionsAttribute(): int
    {
        return $this->quiz->questions->count();
    }

    /**
     * Lấy số câu hỏi trả lời đúng
     */
    public function getCorrectAnswersAttribute(): int
    {
        return $this->attemptAnswers()
            ->where('is_correct', 1)
            ->count();
    }
}