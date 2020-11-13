<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class KeycloakClientServiceProvider  extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\KeycloakClientInterface', function( $app ) {
            return new \App\Services\KeycloakClientService();
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
