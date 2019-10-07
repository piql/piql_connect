<?php


namespace App\Listeners;

use App\Events\ClearTransferStatusEvent;
use App\Events\BagEvent;
use App\Events\ErrorEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ClearTransferStatus implements ShouldQueue
{
    private $amClient;

    public function __construct(ArchivematicaClient $amClient = null)
    {
        $this->amClient = $amClient ?? new ArchivematicaClient();
    }

    public function handle(ClearTransferStatusEvent $event)
    {
        $bag = $event->bag;

        Log::info("Clearing transfer status for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        $currentIngestStatuses = $this->amClient->getTransferStatus();
        foreach($currentIngestStatuses->results as $status)
        {
            if($status->name.".zip" == $bag->zipBagFileName())
            {
                $this->amClient->hideTransferStatus($status->uuid);
            }
        }
    }

}
