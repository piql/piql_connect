<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FilePreviewProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind('App\Interfaces\FilePreviewInterface', function( $app ) {
         	return new \App\Services\FilePreviewService( $app );
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
