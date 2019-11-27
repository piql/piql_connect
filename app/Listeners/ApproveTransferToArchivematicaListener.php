<?php


namespace App\Listeners;

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
            Log::error("Service replied with error message : " . ($response->content->message ?? ""));
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
                    event(new ArchivematicaTransferringEvent($bag));
                } else {
                    Log::error("Approve transfer failed for with bag id " . $bag->id);
                    Log::error("Service replied with error message : " . ($response->content->message ?? ""));
                    event(new ErrorEvent($bag));
                }
                return;
            }
        }
        dispatch( function () use ( $bag ) { event( new ApproveTransferToArchivematicaEvent( $bag ) );  }, now()->addSeconds( 10 ) );
    }
}
