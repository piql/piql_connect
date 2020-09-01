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
            $aip = $event->informationPackage;
            $job = Job::currentJob($event->informationPackage->owner);
            Log::debug("Added new Aip to bucket: ".$aip->external_uuid);

            $aipSize = $aip->size;
            // close job and create a new one if the Aip don't fit
            if(($job->size + $aipSize) > $job->bucketSize) {
                $job->applyTransition('close');
                $job->save();
                $job = Job::currentJob($event->informationPackage->owner);
            }
            $job->aips()->save($aip);
        }
    }
}
