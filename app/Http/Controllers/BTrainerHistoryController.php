<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BTrainerHistoryController extends Controller
{
    public function trainer()
    {
        return $this->hasOne('App\BusinessTrainer');
    }
}
