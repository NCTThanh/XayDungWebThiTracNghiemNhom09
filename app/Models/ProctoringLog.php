<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProctoringLog extends Model {
    protected $fillable = ['attempt_id', 'photo_path', 'violation_details', 'status'];
    protected $casts = ['violation_details' => 'json'];

    public function attempt() {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }
}
