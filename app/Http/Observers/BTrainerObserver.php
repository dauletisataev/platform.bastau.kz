<?php

namespace App\Observers;

use App\BusinessTrainer;
use App\BTrainerHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
/**
 * Listen to BusinessTrainer events
 */

class BTrainerObserver
{
	public function created(BusinessTrainer $trainer)
	{	
		BTrainerHistory::insert("created", 1, NULL, NULL, $trainer->id, 'id');
	}

	public function saved(BusinessTrainer $trainer)
	{
		if ($trainer->wasRecentlyCreated) {
			$action = 'created';
		} else {
			$action = 'updated';
		}
		BTrainerHistory::insert($action, 1, NULL, NULL, $trainer->id, 'id');
	}

	public function updated(BusinessTrainer $trainer)
	{
		$changes = array();
	    foreach($trainer->getDirty() as $key => $value){
	        $original = $trainer->getOriginal($key);
	        $changes[$key] = [
	            'old' => $original,
	            'new' => $value,
	        ];
	    }
	    return $changes;
	}

	public function deleteing(BusinessTrainer $trainer)
	{
		if (Auth::check()) {
			BTrainerHistory::create([
				'action' => 'deleted',
				'actor_id' => Auth::user()->id,
				'trainer_id' => $trainer->id,
				'field' => $trainer->id,
			]);
		}	
	}
}