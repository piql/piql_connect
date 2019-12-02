<?php

namespace App\Listeners;

use App\Events\ArchivematicaGetTransferStatusError;
use App\Events\ArchivematicaTransferError;
use App\Events\ConnectionError;
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
        $transitionTo = 'transferring';
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag, $transitionTo ) ){
            Log::error(" ArchivematicaTransferringListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{$transitionTo}'" );
            return;
        }

        // todo: use transfer UUID in get Transfer status
        $response = $this->amClient->getTransferStatus($bag->storage_properties->transfer_uuid);

        if($response->statusCode != 200) {
            $message = "Transfer failed with error code " . $response->statusCode;
            if (isset($response->contents->error) && ($response->contents->error == true)) {
                $message .= " and error message: " . $response->contents->message;
                event(new ArchivematicaGetTransferStatusError($bag, $message));
            } else {
                event(new ConnectionError($bag, $message));
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        $transferStatus = $response->contents->status;

        if($transferStatus == "COMPLETE")
        {
            Log::info("Transfer complete for with bag id " . $bag->id);
            $bag->storage_properties->sip_uuid = $response->contents->sip_uuid;
            $bag->storage_properties->save();
            event(new ArchivematicaIngestingEvent($bag));
            return;

        } elseif ($transferStatus == "FAILED" || $transferStatus == "USER_INPUT" )
        {
            Log::error("Transfer failed for with bag id " . $bag->id);
            event(new ArchivematicaTransferError($bag, "Transfer failed with status: " . $transferStatus));
            event(new ErrorEvent($bag));
            return;
        }

        dispatch( function () use ( $bag ) { event( new ArchivematicaTransferringEvent( $bag ) );  } )->delay( Carbon::now()->addSeconds( 10 ) );
    }
}
