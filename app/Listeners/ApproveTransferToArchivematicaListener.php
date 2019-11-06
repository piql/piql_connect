<?php


namespace App\Listeners;


use App\ArchivematicaService;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\ErrorEvent;
use Log;

class ApproveTransferToArchivematicaListener extends BagListener
{
    protected $state = "approve_transfer";
    private $amClient;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaClient|null $client
     */
    public function __construct(ArchivematicaClient $client = null)
    {
        $this->amClient = $client ?? new ArchivematicaClient( new ArchivematicaServiceConnection( ArchivematicaService::first()));
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function _handle($event)
    {
        $bag = $event->bag;
        Log::info("Handling ApproveTransferToArchivematicaEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        // todo: use UUID instead of parsing all requests.
        $response = $this->amClient->getUnapprovedList();

        if( $response->statusCode != 200) {
            Log::error("Service replied with error message : " . ($response->content->message ?? ""));
            event(new ErrorEvent($bag));
            return;
        }

        $currentTransferStatuses = $response->contents;
        foreach($currentTransferStatuses->results as $status)
        {
            if($status->directory == $bag->zipBagFileName()) {

                $approveResponse = $this->amClient->approveTransfer($bag->zipBagFileName());
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
        $this->delayedEvent(new ApproveTransferToArchivematicaEvent($bag), now()->addSeconds(10) );
    }

}
