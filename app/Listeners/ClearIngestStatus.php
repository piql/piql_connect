<?php


namespace App\Listeners;

use App\Events\ClearIngestStatusEvent;
use App\Events\BagEvent;
use App\Events\ErrorEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ClearIngestStatus implements ShouldQueue
{
    private $amClient;

    public function __construct()
    {
        $this->amClient = new ArchivematicaClient();
    }

    public function handle(ClearIngestStatusEvent $event)
    {
        $bag = $event->bag;

        Log::info("Clearing ingest status for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        $currentIngestStatuses = $this->amClient->getIngestStatus();
        foreach($currentIngestStatuses->results as $status)
        {
            if($status->name.".zip" == $bag->zipBagFileName())
            {
                $this->amClient->hideIngestStatus($status->uuid);
            }
        }
    }

}
