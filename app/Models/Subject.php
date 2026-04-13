<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $fillable = ['name', 'slug'];
    public $timestamps = true;

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
