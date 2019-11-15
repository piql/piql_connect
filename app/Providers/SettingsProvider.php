<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind('App\Interfaces\SettingsInterface', function( $app ) {
            return new \App\Services\UserSettingsService( $app );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
