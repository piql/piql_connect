<?php


namespace App\Listeners;

use App\ArchivematicaService;
use App\Events\ClearIngestStatusEvent;
use App\Events\BagEvent;
use App\Events\ErrorEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ClearIngestStatus implements ShouldQueue
{
    private $amClient;

    public function __construct(ArchivematicaClient $amClient = null)
    {
        $this->amClient = $amClient ?? new ArchivematicaClient( new ArchivematicaServiceConnection( ArchivematicaService::first()) );
    }

    public function handle(ClearIngestStatusEvent $event)
    {
        $bag = $event->bag;

        Log::info("Clearing ingest status for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        $response = $this->amClient->getIngestStatus();
        if($response->statusCode != 200) {
                $message = "get ingest status failed with error code " . $response->statusCode;
            if (isset($response->content->error) && ($response->content->error == true)) {
                $message += " and error message: " . $response->content->message;
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        foreach($response->contents->results as $status)
        {
            if($status->name.".zip" == $bag->zipBagFileName())
            {
                $hideResponse = $this->amClient->hideIngestStatus($status->uuid);
                if($hideResponse->statusCode != 200) {
                    $message = "hide transfer status failed with error code " . $hideResponse->statusCode;
                    if (isset($hideResponse->content->error) && ($hideResponse->content->error == true)) {
                        $message += " and error message: " . $hideResponse->content->message;
                    }

                    Log::error($message);
                    event(new ErrorEvent($bag));
                    return;
                }
            }
        }
    }

}
