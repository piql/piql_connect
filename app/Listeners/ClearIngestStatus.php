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
    private $dashboardClient;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaDashboardClientInterface
     */
    public function __construct( \App\Interfaces\ArchivematicaDashboardClientInterface $dashboardClient )
    {
        $this->dashboardClient = $dashboardClient;
    }


    public function handle( ClearIngestStatusEvent $event )
    {
        $bag = $event->bag;

        $response = $this->dashboardClient->getIngestStatus();
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
                $hideResponse = $this->dashboardClient->hideIngestStatus($status->uuid);
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
