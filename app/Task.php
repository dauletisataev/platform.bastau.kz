<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $timestamps = false;

    public $fillable = ['task_group_id','task_type_id','description','image','audio','rating','words'];

    public function questions() {
        return $this->hasMany('App\TaskQuestion');
    }
    public function task_group() {
        return $this->belongsTo('App\TaskGroup');
    }

    public function users() {
        return $this->belongsToMany(User::class)->withPivot(['essay','mark1','mark2','mark3','comment']);
    }
}
