<?php

namespace App\Observers;

use App\BusinessTrainer;
use App\BTrainerHistory;
use Illuminate\Support\Facades\Log;

/**
 * Listen to BusinessTrainer events
 */

class BTrainerObserver
{
	public function creating(BusinessTrainer $trainer)
	{
		// BTrainerHistory::create([
		// 	'body' => 
		// ])
	}

	public function updating(BusinessTrainer $trainer)
	{
		$changes = array();
	    foreach($trainer->getDirty() as $key => $value){
	        $original = $trainer->getOriginal($key);
	        $changes[$key] = [
	            'old' => $original,
	            'new' => $value,
	        ];
	    }
	}
}