<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\SurveyQuestion;
class Survey extends Model {
    protected $table = 'survey';
    public $timestamps = false;
    public function questions() { return $this->hasMany(SurveyQuestion::class); }
}