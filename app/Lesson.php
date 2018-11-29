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
        'lesson_template_item_id',
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
}
