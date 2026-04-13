<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {
    protected $fillable = ['user_id', 'plan_name', 'amount', 'status', 'start_date', 'end_date'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isActive() {
        return $this->status === 'active' && (!$this->end_date || $this->end_date->isFuture());
    }
}
