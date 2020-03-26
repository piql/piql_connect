<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use App\Interfaces\FileCollectorInterface;

class FileCollectorProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->singleton( FileCollectorInterface::class, function( $app ) {
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

    public function provides()
    {
        return [FileCollectorInterface::class];
    }
}
