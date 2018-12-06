<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    public $fillable =[
        'name'
    ];

    public $timestamps = false;

    public function districts(){
        return $this->hasMany('App\District');
    }
}
