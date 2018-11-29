<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    public $timestamps = false;

    public $fillable = ['name'];
}
