<?php

namespace App\Http\Middleware;

use Closure;

class isAdminOrCoordinator
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
        $request->user()->authorizeRole(['coordinator', 'administrator']);

        return $next($request);
    }
}
