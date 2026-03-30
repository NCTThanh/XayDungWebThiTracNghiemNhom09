<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Question;


class Quiz extends Model {
    protected $table = 'quiz';

    
    public $timestamps = false;
    protected $fillable = [
        'class_id', 'subject_id', 'title', 'description', 'type', 'file_path',
        'duration', 'start_time', 'end_time', 'shuffle_questions', 
        'shuffle_options', 'show_score', 'allow_retry'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_score' => 'boolean',
        'allow_retry' => 'boolean',
    ];

    public function questions() {
    return $this->hasMany(Question::class, 'quiz_id', 'id');
}

    public function attempts() {
        return $this->hasMany(Attempt::class);
    }
    
}