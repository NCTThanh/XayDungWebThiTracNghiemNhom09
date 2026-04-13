<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {
    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function users() {
        return $this->hasMany(User::class);
    }
}
