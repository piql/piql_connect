<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\ArchivematicaIngestingEvent;
use App\Bag;
use App\Job;
use App\ArchivematicaService;
use App\Traits\BagOperations;
use Carbon\Carbon;
use Log;

class ArchivematicaTransferringListener implements ShouldQueue
{
    use BagOperations;

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


    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle( $event )
    {
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag, "transferring" ) ){
            Log::error(" ArchivematicaTransferringListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{transferring}'" );
            return;
        }

        // todo: use transfer UUID in get Transfer status
        $response = $this->amClient->getTransferStatus();

        if($response->statusCode != 200) {
            $message = " initiate transfer failed with error code " . $response->statusCode;
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

        dispatch( function () use ( $bag ) { event( new ArchivematicaTransferringEvent( $bag ) );  } )->delay( Carbon::now()->addSeconds( 10 ) );
    }
}
