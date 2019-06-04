<?php

namespace App\Providers;

use Auth;
use App\Providers\PiqlUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

Use \App\User as User;

class PiqlAuthProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::provider('piql', function($app, array $config) {
            return new PiqlUserProvider();
        });
    }

}
