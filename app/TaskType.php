<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    public $timestamps = false;

    public $fillable = ['name','description'];
}
