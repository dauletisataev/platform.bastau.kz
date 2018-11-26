<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTrainer extends Model
{	
	protected $table = 'trainers';
    
    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    /*
	* public function groups()
    * {
    *	return $this->hasMany('App\Group');
    * }	
    */
}
