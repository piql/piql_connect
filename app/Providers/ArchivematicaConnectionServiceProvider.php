<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ArchivematicaConnectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Interfaces\ArchivematicConnectionServiceInterface', function( $app ) {
            return new \App\Services\ArchivematicaConnectionService( $app );
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
