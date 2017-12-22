<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

use App\User;

class CheckUserRegistrationSelected
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( is_null(User::getUserRegistration()) ) {
            return redirect()->route('select_registration');
        }

        return $next($request);
    }

}