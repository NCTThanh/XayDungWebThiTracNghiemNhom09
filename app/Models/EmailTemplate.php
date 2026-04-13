<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model {
    protected $fillable = ['name', 'subject', 'body', 'variables', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'variables' => 'json'];

    public static function getTemplate($name) {
        return self::where('name', $name)->where('is_active', true)->first();
    }

    public function render($data = []) {
        $body = $this->body;
        foreach ($data as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}
