<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Tenancy\Affects\Connections\Contracts\ProvidesConfiguration;
use Tenancy\Affects\Connections\Events\Drivers\Configuring;
use Tenancy\Affects\Connections\Events\Resolving;
use Tenancy\Concerns\DispatchesEvents;
use Tenancy\Identification\Contracts\Tenant;

class ResolvingConnectionListener implements ProvidesConfiguration
{
    use DispatchesEvents;

    public function handle(Resolving $event)
    {
        dump(__CLASS__);
        dump($event);
        return $this;
    }

    public function configure(Tenant $tenant): array
    {
        dump(__CLASS__);
        $config = [];

        $this->events()->dispatch(new Configuring($tenant, $config, $this));

        return $config;
    }
}
