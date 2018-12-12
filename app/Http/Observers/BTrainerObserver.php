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
		BTrainerHistory::insert("created", Auth::user()->id, NULL, NULL, $trainer->id, 'id');
	}

	public function deleted(BusinessTrainer $trainer)
	{
		BTrainerHistory::insert("deleted", Auth::user()->id, NULL, NULL, $trainer->id, 'id');
	}
}
