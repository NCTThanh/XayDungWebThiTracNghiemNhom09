<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'student_code',
        'password',
        'role',
        'group_id',
        'survey_done',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // ==================== RELATIONSHIPS ====================

    // Điểm danh (quan trọng - sửa lỗi dashboard)
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'user_id');
    }

    // Kết quả thi
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    // Lịch sử làm bài thi
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    // Lớp do giáo viên dạy
    public function taughtClasses(): HasMany
    {
        return $this->hasMany(Classes::class, 'teacher_id');
    }

    // Lớp sinh viên tham gia
    public function enrolledClasses(): BelongsToMany
    {
        return $this->belongsToMany(Classes::class, 'class_users', 'user_id', 'class_id');
    }

    // Nhóm (nếu có)
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // Các relationship khác
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function firebaseTokens(): HasMany
    {
        return $this->hasMany(FirebaseToken::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ==================== ATTRIBUTE HELPERS ====================

    // Điểm trung bình
    public function getAverageScoreAttribute()
    {
        return $this->results()->avg('score') ?? 0;
    }

    // Xếp hạng (có thể mở rộng sau)
    public function getRankAttribute()
    {
        return 'Top 10%'; // Bạn có thể tính động sau
    }

    // ==================== ROLE CHECKERS ====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    // Kiểm tra subscription premium
    public function hasPremium(): bool
    {
        return $this->subscriptions()
                    ->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->exists();
    }
}