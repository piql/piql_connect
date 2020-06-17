<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserDisabled
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
        if (auth()->check() && auth()->user()->disabled_on != null) {
            auth()->logout();
            return redirect()->route('login')->withMessage('Your account is disabled');
        }

        return $next($request);
    }
}
