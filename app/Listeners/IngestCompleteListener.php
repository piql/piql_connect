<?php

namespace App\Listeners;

use App\Events\ClearTmpFilesEvent;
use App\Events\ClearTransferStatusEvent;
use App\Events\ClearIngestStatusEvent;
use App\Events\IngestCompleteEvent;
use App\Job;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Bag;
use App\Traits\BagOperations;

class IngestCompleteListener implements ShouldQueue
{
    use BagOperations;

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
        $transitionTo = 'complete';
        $bag = $event->bag->refresh();
        if( !$this->tryBagTransition( $bag, $transitionTo ) ){
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{$transitionTo}'" );
            return;
        }

        Job::currentJob($bag->owner)->bags()->toggle($bag);
        Log::info("Ingest complete: ".$bag->id." status: ".$bag->status);

        event( new ClearTransferStatusEvent($bag));
        event( new ClearIngestStatusEvent($bag));
        event( new ClearTmpFilesEvent($bag));
    }
}
