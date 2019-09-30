<?php

namespace App\Listeners;

use App\Events\ArchivematicaIngestingEvent;
use App\Events\ErrorEvent;
use App\Events\IngestCompleteEvent;
use App\Events\StartTransferToArchivematicaEvent;
use App\StorageProperties;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Suppoer\Facades\Storage;
use GuzzleHttp\Client as Guzzle;
use Log;
use App\Bag;
use App\Job;
use App\ArchivematicaService;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class ArchivematicaIngestingListener  extends BagListener
{
    protected $state = "ingesting";
    private $amClient;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaClient|null $amClient
     */
    public function __construct(ArchivematicaClient $amClient = null)
    {
        $this->amClient = $amClient ?? new ArchivematicaClient();
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

        $currentIngestStatuses = $this->amClient->getIngestStatus();
        foreach($currentIngestStatuses->results as $status)
        {
            if($status->name.".zip" == $bag->zipBagFileName())
            {
                if($status->status == "COMPLETE" || env('APP_DEBUG_SKIP_INGEST_STATUS', false))
                {
                    if(env('APP_DEBUG_SKIP_INGEST_STATUS', false))
                    {
                        Log::info("Ingest status bypass for SIP with bag id ".$bag->id);
                    }

                    Log::info("Ingest complete for SIP with bag id ".$bag->id); //." with aip uuid: ".$status->uuid);

                    $bag->storage_properties->update( ['aip_uuid' =>  $status->uuid] );

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

        $this->delayedEvent(new ArchivematicaIngestingEvent($bag), now()->addSeconds(10) );

    }

}
