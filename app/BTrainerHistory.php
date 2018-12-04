<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BTrainerHistory extends Model
{
    public function history()
    {
    	return this->belongsTo(BusinessTrainer::class, 'trainer_id')
    }
}
