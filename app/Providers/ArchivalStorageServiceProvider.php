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
            $filesystemDriver = $this->app->make('App\Interfaces\FilesystemDriverInterface');
            return new \App\Services\ArchivalStorageService( $app, $filesystemDriver );
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
