<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = ['key', 'value', 'type', 'description'];
    
    public static function get($key, $default = null) {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value, $type = 'string') {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    public static function getBoolean($key) {
        $value = self::get($key);
        return in_array($value, [true, 1, '1', 'true', 'yes']);
    }
}
