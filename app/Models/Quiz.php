<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model {
    protected $table = 'quiz';

    public $timestamps = true;
    protected $fillable = [
        'class_id', 'subject_id', 'title', 'description', 'type', 'file_path',
        'duration', 'start_time', 'end_time', 'shuffle_questions', 
        'shuffle_options', 'show_score', 'allow_retry', 'pass_score', 'is_published'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_score' => 'boolean',
        'allow_retry' => 'boolean',
        'is_published' => 'boolean',
        'pass_score' => 'float',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'quiz_id', 'id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'quiz_id', 'id');
    }
}