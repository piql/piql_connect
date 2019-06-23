<?php

namespace App\Listeners;

use App\Events\ReceivedStatusFromArchivematicaEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Bag;

class UpdateIngestStatusListener implements ShouldQueue
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
     * @param  ReceivedStatusFromArchivematicaEvent  $event
     * @return void
     */
    public function handle(ReceivedStatusFromArchivematicaEvent $event)
    {
        $bag = $event->bag;
        Log::info("Received status from Archivematica: ".$bag->id." status: ".$bag->status);
        sleep(30);
        $bag->status = "complete";
        $bag->save();
    }
}
