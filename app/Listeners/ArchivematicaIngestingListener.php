<?php

namespace App\Listeners;

use App\Events\ErrorEvent;
use App\Events\IngestCompleteEvent;
use App\Events\StartTransferToArchivematicaEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Suppoer\Facades\Storage;
use GuzzleHttp\Client as Guzzle;
use Log;
use App\Bag;
use App\Job;
use App\ArchivematicaService;

class ArchivematicaIngestingListener  extends BagListener
{
    protected $state = "ingesting";
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
        Log::info("Handling ArchivematicaIngestingEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        while(true)
        {
            $currentIngestStatuses = $this->amClient->getIngestStatus();
            foreach($currentIngestStatuses->results as $status)
            {
                if($status->name.".zip" == $bag->zipBagFileName() && $status->status == "COMPLETE")
                {
                    if($status->status == "COMPLETE")
                    {
                        Log::info("Ingest complete for SIP with bag id ".$bag->id);
                        event(new IngestCompleteEvent($bag));
                        return;
                    } elseif ($status->status == "FAILED" || $status->status == "USER_INPUT" )
                    {
                        Log::error("Ingest failed for with bag id " . $bag->id);
                        event(new ErrorEvent($bag));
                        return;
                    }

                }
            }
            sleep(2);
        }

    }

}
