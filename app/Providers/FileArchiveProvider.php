<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FileArchiveProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\FileArchiveInterface', function( $app ) {
            $archivalStorageService = $app->make('App\Interfaces\ArchivalStorageInterface');
            return new \App\Services\FileArchiveService( $app, $archivalStorageService );
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
