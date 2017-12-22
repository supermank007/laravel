<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CheckRole
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (! $request->user()->hasRoles(...$roles)) {
            return new Response(view('access-denied'));
        }

        return $next($request);
    }

}