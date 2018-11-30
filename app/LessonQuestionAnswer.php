<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonQuestionAnswer extends Model
{
    public $timestamps = false;

    public $fillable = [
        'lesson_question_id',
        'option',
        'value',
        'is_correct'
    ];
}
