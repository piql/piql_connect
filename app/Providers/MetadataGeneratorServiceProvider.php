<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class MetadataGeneratorServiceProvider  extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\MetadataGeneratorInterface', function( $app ) {
            return new \App\Services\MetadataGeneratorService( $app);
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
