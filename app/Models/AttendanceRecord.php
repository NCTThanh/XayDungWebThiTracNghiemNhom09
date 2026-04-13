<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'scan_time',
        'status',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
    ];

    // Quan hệ: Bản ghi thuộc về phiên điểm danh nào
    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    // Quan hệ: Sinh viên nào đã điểm danh
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}