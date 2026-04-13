<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // 1. Tắt tự động thêm created_at, updated_at
    public $timestamps = true; 

    // 2. Khai báo các cột được phép Insert dữ liệu vào
    protected $fillable = ['quiz_id', 'subject_id', 'question', 'image_url', 'type', 'difficulty', 'marks'];

    // 3. Liên kết tới bảng đáp án
    public function options() {
        return $this->hasMany(Option::class, 'question_id', 'id');
    }

    // 4. Liên kết tới bảng môn thi
    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    // 5. Liên kết tới bảng quiz
    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }
}