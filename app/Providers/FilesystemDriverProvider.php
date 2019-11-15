<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FilesystemDriverProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind('App\Interfaces\FilesystemDriverInterface', function( $app ) {
            return new \App\Services\FilesystemDriverService( $app );
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
