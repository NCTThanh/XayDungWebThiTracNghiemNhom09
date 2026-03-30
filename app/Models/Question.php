<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // 1. Tắt tự động thêm created_at, updated_at
    public $timestamps = false; 

    // 2. Khai báo các cột được phép Insert dữ liệu vào
    protected $fillable = ['quiz_id', 'question'];

    // 3. Liên kết tới bảng đáp án
    public function options() {
        return $this->hasMany(Option::class, 'question_id', 'id');
    }
}