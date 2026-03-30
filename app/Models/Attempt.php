<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttemptAnswer;
use App\Models\Submission;
use App\Models\Quiz;
use App\Models\User;

class Attempt extends Model {
    protected $fillable = [
        'user_id', 'quiz_id', 'start_time', 'end_time', 
        'status', 'score', 'cheat_warnings'
    ];
    
   
    protected function casts(): array {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function answers() {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function submission() {
        return $this->hasOne(Submission::class);
    }
    
    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Bổ sung quan hệ với User vì bạn có trường 'user_id' trong fillable
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}