<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    protected $fillable = ['user_id', 'action', 'model', 'model_id', 'details', 'ip_address'];
    protected $casts = ['details' => 'json'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $user, $model = null, $modelId = null, $details = null) {
        return self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => request()->ip()
        ]);
    }
}
