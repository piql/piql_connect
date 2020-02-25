<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class SessionLastActivity
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
        /* Laravels SESSION_LIFETIME is in minutes, 
         * convert to milliseconds for easy handling in javascript */
        Session::put('sessionLifetimeMs', env('SESSION_LIFETIME', 120 ) * 60000 );
        /* If lastActivityTime changes, the logout counter will be reset */
        Session::put('lastActivityTime', time() * 1000 );
        return $next($request);
    }
}
