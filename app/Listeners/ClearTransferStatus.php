<?php


namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\ArchivematicaService;
use App\Events\ClearTransferStatusEvent;
use App\Events\BagEvent;
use App\Events\ErrorEvent;
use Log;

class ClearTransferStatus implements ShouldQueue
{
    private $amClient;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaDashboardClientInterface
     */
    public function __construct( \App\Interfaces\ArchivematicaDashboardClientInterface $dashboardClient )
    {
        $this->amClient = $dashboardClient;
    }

    public function handle(ClearTransferStatusEvent $event)
    {
        $bag = $event->bag;

        Log::info("Clearing transfer status for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        $hideResponse = $this->amClient->hideTransferStatus($bag->storage_properties->transfer_uuid);
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
