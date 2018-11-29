<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    public $timestamps = false;

    public $fillable = ['is_homework','is_test','section_id','lesson_id','material_id','test_id','lesson_template_item_id'];

    public function tasks() {
        return $this->hasMany('App\Task');
    }

    public function students() {
        return $this->belongsToMany('App\Student','task_group_student');
    }

    public function section() {
        return $this->belongsTo('App\Section');
    }

    public function material() {
        return $this->belongsTo('App\Material');
    }

    public function test() {
        return $this->belongsTo('App\Test');
    }

    public function lesson() {
        return $this->belongsTo('App\Lesson');
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function lesson_template_item() {
        return $this->belongsTo(LessonTemplateItem::class);
    }
}


