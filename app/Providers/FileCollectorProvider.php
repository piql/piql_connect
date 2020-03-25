<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FileCollectorProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind('App\Interfaces\FileCollectorInterface', function( $app ) {
            return new \App\Services\TarFileService( $app );
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
