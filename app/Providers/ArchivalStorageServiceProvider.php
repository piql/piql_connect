<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ArchivalStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\ArchivalStorageInterface', function( $app ) {
            return new \App\Services\ArchivalStorageService($app);
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot( )
    {
    }

}
