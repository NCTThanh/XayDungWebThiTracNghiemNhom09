<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $fillable = ['user_id', 'quiz_id', 'gateway', 'transaction_id', 'amount', 'currency', 'status', 'response_data'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }

    public static function isGatewayEnabled($gateway) {
        $gateways = config('quiz.gateways');
        return isset($gateways[$gateway]) && !empty($gateways[$gateway]);
    }
}
