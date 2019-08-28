<?php

namespace App\Listeners;

use App\Events\ClearTransferStatusEvent;
use App\Events\ClearIngestStatusEvent;
use App\Events\IngestCompleteEvent;
use App\Job;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Bag;

class IngestCompleteListener extends BagListener
{
    protected $state = "complete";
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
     * @param  IngestCompleteEvent  $event
     * @return void
     */
    public function handle($event)
    {
        $bag = $event->bag;
        Job::currentJob($bag->owner)->bags()->toggle($bag);
        Log::info("Ingest complete: ".$bag->id." status: ".$bag->status);

        event( new ClearTransferStatusEvent($bag));
        event( new ClearIngestStatusEvent($bag));
    }
}
