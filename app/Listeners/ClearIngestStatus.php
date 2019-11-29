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

        $hideResponse = $this->dashboardClient->hideIngestStatus($bag->storage_properties->sip_uuid);
        if($hideResponse->statusCode != 200) {
            $message = "hide transfer status failed with error code " . $hideResponse->statusCode;
            if (isset($hideResponse->contents->error) && ($hideResponse->contents->error == true)) {
                $message .= " and error message: " . $hideResponse->contents->message;
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }
    }

}
