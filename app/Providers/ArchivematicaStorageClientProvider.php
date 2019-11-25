<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ArchivematicaStorageClientProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind( 'App\Interfaces\ArchivematicaStorageClientInterface', function( $app ) {
            $connectionService = $app->make( 'App\Interfaces\ArchivematicaConnectionServiceInterface' );
            return new \App\Services\ArchivematicaStorageClientService( $connectionService );
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
