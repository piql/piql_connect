<?php

namespace App\Providers;

use App\Interfaces\PreProcessBagInterface;
use Illuminate\Support\ServiceProvider;

class IngestValidationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('App\Interfaces\IngestValidationInterface', function ($app) {
            $className = config( "piql_connect.service.ingest_validation",
                env( 'APP_INGEST_VALIDATION_SERVICE', "App\Services\IngestValidationService"));
            config( ["piql_connect.service.ingest_validation" => $className]);
            return new $className($this->app);
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
