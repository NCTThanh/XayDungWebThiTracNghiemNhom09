<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'qr_token',
        'expiry_time',
        'created_by',
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
    ];

    // Quan hệ: 1 Phiên điểm danh có nhiều bản ghi điểm danh
    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    // Quan hệ: Người tạo (Giáo viên)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}