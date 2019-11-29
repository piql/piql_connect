<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use App\Events\ArchivematicaIngestingEvent;
use App\Events\ErrorEvent;
use App\Events\IngestCompleteEvent;
use App\Bag;
use App\Traits\BagOperations;
use Log;

class ArchivematicaIngestingListener implements ShouldQueue
{
    use BagOperations;

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

    /**
     * Handle event for reading back status from Archivematica from started ingests.
     *
     * @param  object  $event
     * @return void
     */
    public function handle( $event )
    {
        $transitionTo = 'ingesting';
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag, $transitionTo ) ){
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{$transitionTo}'" );
            return;
        }

        // todo: use ingest UUID to retrieve status
        $response = $this->dashboardClient->getIngestStatus($bag->storage_properties->sip_uuid);

        if($response->statusCode != 200) {
            $message = "ingest status failed with error code " . $response->statusCode;
            if (isset($response->contents->error) && ($response->contents->error == true)) {
                $message .= " and error message: " . $response->contents->message;
            }

            Log::error( $message );
            event( new ErrorEvent( $bag ) );
            return;
        }

        $ingestStatus = $response->contents->status;

        if($ingestStatus == "COMPLETE" || env('APP_DEBUG_SKIP_INGEST_STATUS', false))
        {
            if(env('APP_DEBUG_SKIP_INGEST_STATUS', false))
            {
                Log::info("Ingest status bypass for SIP with bag id ".$bag->id);
            }

            Log::info("Ingest complete for SIP with bag id ".$bag->id); //." with aip uuid: ".$status->uuid);


            event(new IngestCompleteEvent($bag));
            return;
        } elseif ($ingestStatus == "FAILED" || $ingestStatus == "USER_INPUT" )
        {
            event( new ErrorEvent( $bag ) );
            return;
        }

        dispatch( function () use ( $bag ) { event( new ArchivematicaIngestingEvent( $bag ) );  } )->delay( Carbon::now()->addSeconds( 10 ) );
    }
}
