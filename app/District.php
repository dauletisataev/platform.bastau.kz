<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';

    public $fillable =[
        'name',
        'region_id',
    ];

    public $timestamps = false;

    public function region(){
        return $this->belongsTo('App\Region');
    }
    public function localities(){
        return $this->hasMany('App\Locality');
    }
}
