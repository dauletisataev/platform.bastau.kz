<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BTrainerLogController extends Controller
{
    public function trainer()
    {
        return $this->hasOne('App\BusinessTrainer');
    }
}
