<?php

namespace App\Listeners;

use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\ArchivematicaIngestingEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Suppoer\Facades\Storage;
use GuzzleHttp\Client as Guzzle;
use Log;
use App\Bag;
use App\Job;
use App\ArchivematicaService;

class ArchivematicaTransferringListener  extends BagListener
{
    protected $state = "transferring";
    private $amClient;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->amClient = new ArchivematicaClient();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function _handle($event)
    {
        $bag = $event->bag;

        Log::info("Handling ArchivematicaTransferringEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        $currentIngestStatuses = $this->amClient->getTransferStatus();
        foreach($currentIngestStatuses->results as $status)
        {
            if($status->name.".zip" == $bag->zipBagFileName())
            {
                if($status->status == "COMPLETE")
                {
                    Log::info("Transfer complete for with bag id " . $bag->id);
                    event(new ArchivematicaIngestingEvent($bag));
                    return;
                } elseif ($status->status == "FAILED" || $status->status == "USER_INPUT" )
                {
                    Log::error("Transfer failed for with bag id " . $bag->id);
                    event(new ErrorEvent($bag));
                    return;
                }
            }
        }

        $this->delayedEvent(new ArchivematicaTransferringEvent($bag), now()->addSeconds(10) );

    }

}
