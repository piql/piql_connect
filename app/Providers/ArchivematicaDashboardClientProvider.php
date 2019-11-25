<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ArchivematicaDashboardClientProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\ArchivematicaDashboardClientInterface', function( $app ) {
            $connectionService = $app->make( 'App\Interfaces\ArchivematicaConnectionServiceInterface' );
            return new \App\Services\ArchivematicaDashboardClientService( $connectionService );
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
