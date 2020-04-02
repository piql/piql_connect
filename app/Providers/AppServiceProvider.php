<?php

namespace App\Providers;

use App\ArchivematicaService;
use App\Customer;
use App\User;
use App\Listeners\ArchivematicaServiceConnection;
use App\Listeners\SendBagToArchivematicaListener;
use App\Services\ArchivematicaConnectionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Tenancy\Identification\Contracts\ResolvesTenants;

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

        $this->app->bind('App\Interfaces\ArchivematicaConnectionServiceInterface', function ($app) {
            return new ArchivematicaConnectionService($this->app);
        });

        $this->app->resolving(ResolvesTenants::class, function (ResolvesTenants $resolver) {
            $resolver->addModel(Customer::class);
        });

        //$this->app->resolving(ResolvesTenants::class, function (ResolvesTenants $resolver) {
        //    $resolver->addModel(User::class);
        //});
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
