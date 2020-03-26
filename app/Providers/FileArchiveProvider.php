<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\FileArchiveInterface;
use App\Interfaces\ArchivalStorageInterface;
use App\Interfaces\FileCollectorInterface;
use Illuminate\Contracts\Support\DeferrableProvider;

class FileArchiveProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FileArchiveInterface::class, function( $app ) {
            $archivalStorageService = $app->make(ArchivalStorageInterface::class);
            $fileCollectorService = $app->make( FileCollectorInterface::class );
            return new \App\Services\FileArchiveService( $app, $archivalStorageService, $fileCollectorService );
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

    public function provides()
    {
        return [FileArchiveInterface::class];
    }
}
