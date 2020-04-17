<?php

namespace App\Providers;

use App\Interfaces\PreProcessBagInterface;
use Illuminate\Support\ServiceProvider;

class PreProcessBagServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('App\Interfaces\PreProcessBagInterface', function ($app) {
            $className = config( "piql_connect.service.pre_process_bag",
                env( 'APP_PRE_PROCESS_BAG_SERVICE', "App\Services\PreProcessBagService"));
            config( ["piql_connect.service.pre_process_bag" => $className]);
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
