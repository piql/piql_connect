<?php

namespace App\Listeners;

use App\Events\BagCompleteEvent;
use App\Events\ReceivedStatusFromArchivematicaEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Bag;

class SendBagToArchivematicaListener implements ShouldQueue
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
     * @param  BagCompleteEvent  $event
     * @return void
     */
    public function handle(BagCompleteEvent $event)
    {
        Log::debug("Handling BagCompleteEvent");
        $bag = $event->bag;
        $bag->status = "processing";
        $bag->save();
        event( new ReceivedStatusFromArchivematicaEvent($bag) );
    }
}
