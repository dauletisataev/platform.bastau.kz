<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendPulseToken extends Model
{
    public $fillable = [
        'id',
        'token',
        'expires_in' 
    ];
}
