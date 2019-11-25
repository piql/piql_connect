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

        $response = $this->amClient->getTransferStatus();
        if($response->statusCode != 200) {
            $message = "get transfer status failed with error code " . $response->statusCode;
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
                $hideResponse = $this->amClient->hideTransferStatus($status->uuid);
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
