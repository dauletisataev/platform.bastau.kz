<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonQuestion extends Model
{
    public $timestamps = false;

    public $fillable = [
        'lesson_template_id',
        'value'
    ];

    public function answers() {
        return $this->hasMany('App\LessonQuestionAnswer');
    }
}
