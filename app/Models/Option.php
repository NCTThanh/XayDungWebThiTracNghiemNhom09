<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    // 1. Tắt tự động thêm created_at, updated_at
    public $timestamps = false; 

    // 2. Khai báo các cột được phép Insert
    protected $fillable = ['question_id', 'option_text', 'is_correct'];
    // Lưu ý: Nếu cột lưu đáp án trong DB của bạn tên là 'content' hay tên khác, 
    // hãy sửa chữ 'option_text' lại cho đúng nhé.
}