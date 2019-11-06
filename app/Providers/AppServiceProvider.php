<?php

namespace App\Providers;

use App\ArchivematicaService;
use App\Listeners\ArchivematicaServiceConnection;
use App\Listeners\SendBagToArchivematicaListener;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal() ) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind('App\Listeners\ArchivematicaServiceConnection', function ($app) {
            return new ArchivematicaServiceConnection( ArchivematicaService::first());
        });

        $this->app->bind('App\Listeners\SendBagToArchivematicaListener', function ($app) {
            return new SendBagToArchivematicaListener( Storage::disk('am'));
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
