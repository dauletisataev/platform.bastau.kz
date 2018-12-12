<?php

namespace App\Http\Middleware;

use Closure;
/**
 * Yersultan
 * Required for coordinators
 */
class isCoordinator
{

    public function handle($request, Closure $next)
    {
        $request->user()->authorizeRole(['administrator']);

        return $next($request);
    }
}
