<?php

namespace App\Listeners;

use App\Events\InformationPackageUploaded;
use App\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Log;

class AddAipToBucketListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InformationPackageUploaded  $event
     * @return void
     */
    public function handle($event)
    {
        if(get_class($event->informationPackage) == 'App\Aip') {
            // todo: bucket size limiting
            Log::debug("Added new Aip to bucket: ".$event->informationPackage->external_uuid);
            Job::currentJob($event->informationPackage->owner)->aips()->save($event->informationPackage);
        }
        //Log::info("Ingest complete: ".$bag->id." status: ".$bag->status);

    }
}
