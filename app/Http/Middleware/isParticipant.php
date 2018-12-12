<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
class IsParticipant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->user()->authorizeRole(['Participant']);

        return $next($request);
    }
}