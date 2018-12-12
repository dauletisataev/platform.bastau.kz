<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    public $timestamps = false;

    public $fillable = [
        'title',
        'passed',
        'datetime',
        'without_date',
        'duration',
        'is_full',
        'manual',
        'is_started',
        'lesson_template_id',
        "group_id",
        'order',
        'access_tasks',
        'access_homework',
        'access_tests',
        'access_offset',
        'access_offset_value',
        'access_offset_type'
    ];

    public function toArray()
    {
        $array = parent::toArray();
        return $array;
    }

    public function homework() {
        return $this->hasMany('App\TaskGroup');
    }

    public function pages() {
        return $this->hasMany('App\MaterialPage');
    }

    public function materials() {
        return $this->belongsToMany('App\Material');
    }

    public function group(){
        return $this->belongsTo('App\Group');
    }
}
