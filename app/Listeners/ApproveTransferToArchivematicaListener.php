<?php


namespace App\Listeners;

use App\Events\ArchivematicaApproveTransferError;
use App\Events\ArchivematicaGetUnapprovedListError;
use App\Events\ArchivematicaInitiateTransferError;
use App\Events\ConnectionError;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ArchivematicaService;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\ErrorEvent;
use App\Interfaces\ArchivematicaDashboardClientInterface;
use App\Traits\BagOperations;
use Log;

class ApproveTransferToArchivematicaListener implements ShouldQueue
{
    use BagOperations;

    private $dashboardClient;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaDashboardClientInterface
     */
    public function __construct( ArchivematicaDashboardClientInterface $dashboardClient )
    {
        $this->dashboardClient = $dashboardClient;
    }

    /**
     * Handle Approve Transfer To Archivematica event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle( $event )
    {
        $transitionTo = 'approve_transfer';
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag,  $transitionTo) ){
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{$transitionTo}'" );
            return;
        }

        // todo: use UUID instead of parsing all requests.
        $response = $this->dashboardClient->getUnapprovedList();

        if( $response->statusCode != 200) {
            $message = "Get unapproved list failed with error code " . $response->statusCode;
            if (isset($response->contents->error) && ($response->contents->error == true)) {
                $message .= " and error message: " . $response->contents->message;
                event(new ArchivematicaGetUnapprovedListError($bag, $message));
            } else {
                event(new ConnectionError($bag, $message));
            }
            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        $currentTransferStatuses = $response->contents;
        foreach($currentTransferStatuses->results as $status)
        {
            if($status->directory == $bag->zipBagFileName()) {

                $approveResponse = $this->dashboardClient->approveTransfer($bag->zipBagFileName());
                // todo: we need to take case of transfer UUID
                if ($approveResponse->statusCode == 200) {
                    $bag->storage_properties->transfer_uuid = $approveResponse->contents->uuid;
                    $bag->storage_properties->save();
                    event(new ArchivematicaTransferringEvent($bag));
                } else {
                    $message = "Approve transfer failed with error code " . $approveResponse->statusCode;
                    if (isset($approveResponse->contents->error) && ($approveResponse->contents->error == true)) {
                        $message .= " and error message: " . $approveResponse->contents->message;
                        event(new ArchivematicaApproveTransferError($bag, $message));
                    } else {
                        event(new ConnectionError($bag, $message));
                    }
                    Log::error("Approve transfer failed for with bag id " . $bag->id);
                    Log::error("Service replied with error message : " . ($approveResponse->content->message ?? ""));
                    event(new ErrorEvent($bag));
                }
                return;
            }
        }
        dispatch( function () use ( $bag ) { event( new ApproveTransferToArchivematicaEvent( $bag ) );  } )->delay( now()->addSeconds( 10 ) );
    }
}
