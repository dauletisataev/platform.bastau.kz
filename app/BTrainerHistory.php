<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Barryvdh\Debugbar\LaravelDebugbar;

class BTrainerHistory extends Model
{
    protected $fillable = [
        'action_name', 
        'actor_id', 
        'old_value', 
        'new_value', 
        'trainer_id', 
        'field'
    ];
    protected $table = 'bthistories';
    protected $primaryKey = 'id';
    
    public function trainer()
    {
    	return $this->belongsTo(BusinessTrainer::class, 'trainer_id', 'id');
    }

    public static function insert($action_name, $actor, $old, $new, $trainer, $field)
    {
    	BTrainerHistory::create([
    		'action_name' => $action_name,
    		'actor_id' => $actor,
    		'old_value' => $old,
    		'new_value' => $new,
    		'trainer_id' => $trainer,
    		'field' => $field,
    	]);
    }
}
