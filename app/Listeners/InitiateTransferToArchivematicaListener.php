<?php


namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\ArchivematicaService;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ErrorEvent;
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
        if( !$this->tryBagTransition( $bag, $transitionTo ) ){
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{$transitionTo}'" );
            return;
        }

        $response = $this->dashboardClient->initiateTransfer($bag->name, $bag->uuid, $bag->zipBagFileName());

        if($response->statusCode != 200) {
            $message = "Initiate transfer failed with error code " . $response->statusCode;

            if (isset($response->contents->error) && ($response->contents->error == true)) {
                $message .= " and error message: " . $response->contents->message;
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        event(new ApproveTransferToArchivematicaEvent($bag));
    }
}
