<?php


namespace App\Listeners;

use App\Events\ArchivematicaInitiateTransferError;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Events\ConnectionError;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ArchivematicaService;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ErrorEvent;
use Carbon\Carbon;
use Log;
use App\Traits\BagOperations;

class InitiateTransferToArchivematicaListener implements ShouldQueue
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
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $transitionTo = 'initiate_transfer';
        $bag = $event->bag->refresh();
        if( !$this->tryBagTransition( $bag, $transitionTo ) ) {
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{$transitionTo}'" );
            return;
        }

        $response = $this->dashboardClient->initiateTransfer($bag->zipBagFileName(), $bag->uuid, $bag->zipBagFileName());

        if($response->statusCode != 200) {
            $message = "Initiate transfer failed with error code " . $response->statusCode;

            if (isset($response->contents->error) && ($response->contents->error == true)) {
                $message .= " and error message: " . $response->contents->message;
                if($response->statusCode == 500) { // a bit strange error code it seams that
                    Log::debug( $message );
                    Log::debug( "Retransmitting InitiateTransferToArchivematicaEvent" );
                    dispatch( function () use ( $bag ) {
                        event( new InitiateTransferToArchivematicaEvent( $bag ) );
                    } )->delay( Carbon::now()->addSeconds( 10 ) );
                    return;
                }
                event(new ArchivematicaInitiateTransferError($bag, $message));
            } else {
                event(new ConnectionError($bag, $message));
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        event(new ApproveTransferToArchivematicaEvent($bag));
    }
}
