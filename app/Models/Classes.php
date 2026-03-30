<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model {
    protected $fillable = ['teacher_id', 'name', 'join_code', 'description', 'is_active'];

    public function teacher(): BelongsTo {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany {
        return $this->belongsToMany(User::class, 'class_users', 'class_id', 'user_id');
    }

    public function quizzes(): HasMany {
        return $this->hasMany(Quiz::class, 'class_id');
    }
}