<?php 
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable {
   
    public $timestamps = false; 

    protected $fillable = [
        'name', 
        'email', 
        'student_code', 
        'password', 
        'role', 
        'survey_done'
    ];

    protected $hidden = ['password', 'remember_token'];

    // Lớp do giáo viên tạo
    public function taughtClasses(): HasMany {
        return $this->hasMany(Classes::class, 'teacher_id');
    }

    // Lớp sinh viên tham gia
    public function enrolledClasses(): BelongsToMany {
        return $this->belongsToMany(Classes::class, 'class_users', 'user_id', 'class_id');
    }

    public function attempts(): HasMany {
        return $this->hasMany(Attempt::class);
    }
    
    // Check Role Helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isStudent(): bool { return $this->role === 'student'; }
}