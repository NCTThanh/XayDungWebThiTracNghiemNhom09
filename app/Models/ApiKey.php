<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model {
    protected $fillable = ['user_id', 'key', 'app_name', 'permissions', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'permissions' => 'json'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->key) {
                $model->key = 'api_' . Str::random(40);
            }
        });
    }

    public static function generateKey($userId, $appName) {
        return self::create([
            'user_id' => $userId,
            'app_name' => $appName,
            'is_active' => true
        ]);
    }

    public static function validateKey($key) {
        return self::where('key', $key)->where('is_active', true)->first();
    }
}
