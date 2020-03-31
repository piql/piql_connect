<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MetsParserService;

class MetsParserProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Interfaces\MetsParserInterface::class, function( $app ) {
            return new MetsParserService( $app );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
