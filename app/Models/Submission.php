<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model {
    protected $fillable = ['attempt_id', 'file_path', 'teacher_feedback', 'manual_score'];
}