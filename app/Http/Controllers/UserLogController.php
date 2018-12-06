<?php

namespace App\Http\Controllers;

use App\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class UserLogController extends Controller
{
    public function items(Request $request)
    {
            return UserLog::with('user')->filter(Input::all())->orderBy('created_at', 'desc')->paginate(20);
    }
}
