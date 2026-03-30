<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    // Tắt timestamps nếu database cũ của bạn không có created_at/updated_at
    public $timestamps = false; 

    // KHAI BÁO CÁC CỘT ĐƯỢC PHÉP LƯU
    protected $fillable = [
        'user_id', 
        'quiz_id', 
        'start_time', 
        'status', 
        'cheat_warnings'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }
}