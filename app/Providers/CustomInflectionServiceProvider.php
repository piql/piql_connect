<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\Common\Inflector\Inflector;

class CustomInflectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Inflector::rules('plural', ['irregular' => ['fonds' => 'fonds']] );
        Inflector::rules('plural', ['irregular' => ['mets' => 'mets']] );
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
