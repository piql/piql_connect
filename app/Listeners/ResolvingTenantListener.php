<?php

namespace App\Listeners;

use App\Customer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Tenancy\Affects\Connections\Contracts\ProvidesConfiguration;
use Tenancy\Affects\Connections\Events\Drivers\Configuring;
use Tenancy\Identification\Events\Resolving;
use Tenancy\Concerns\DispatchesEvents;
use Tenancy\Identification\Contracts\Tenant;

class ResolvingTenantListener
{
    use DispatchesEvents;

    public function handle(Resolving $event)
    {
        dump(__CLASS__);
        dump($event->models);
        return null;
    }

}
