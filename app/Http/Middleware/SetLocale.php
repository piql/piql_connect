<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class SetLocale
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
        $userSettings = $request->user()->settings;
        if( isset($userSettings) )
        {
            $locale = $userSettings->interfaceLanguage;
            \App::setLocale($locale);
        }
        return $next($request);
    }
}
