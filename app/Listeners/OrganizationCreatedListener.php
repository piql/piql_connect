<?php


namespace App\Listeners;

use App\Events\OrganizationCreatedEvent as OrganizationCreated;
use Log;

class OrganizationCreatedListener
{
    /**
     * Handle Bag Error events.
     *
     * @param  OrganizationCreated $event
     * @return void
     */
    public function handle( OrganizationCreated $event )
    {
        $org = $event->organization;
        Log::info( "Organization({$org->uuid}) {$org->name} has been created");
    }

}
