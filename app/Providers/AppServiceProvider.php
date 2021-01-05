<?php

namespace App\Providers;

use App\ArchivematicaService;
use App\Listeners\ArchivematicaServiceConnection;
use App\Listeners\ClearTmpFiles;
use App\Listeners\SendBagToArchivematicaListener;
use App\Services\ArchivematicaConnectionService;
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
            return new SendBagToArchivematicaListener( Storage::disk('bags'), Storage::disk('am'));
        });

        $this->app->bind(ClearTmpFiles::class, function ($app) {
            return new ClearTmpFiles( Storage::disk('am'));
        });

        $this->app->bind('App\Interfaces\ArchivematicaConnectionServiceInterface', function ($app) {
            return new ArchivematicaConnectionService($this->app);
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
        if(version_compare(phpversion(), '7.4', '>=')) {
            error_reporting(E_ALL ^ E_DEPRECATED);
        }
    }
}
