<?php


namespace App\Listeners;


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
        $this->amClient = $client ?? new ArchivematicaClient();
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

        $currentTransferStatuses = $this->amClient->getTransferStatus();
        foreach($currentTransferStatuses->results as $status)
        {
            if($status->name == $bag->zipBagFileName())
            {
                try {
                    $this->amClient->approveTransfer($bag->id);
                    event(new ArchivematicaTransferringEvent($bag));
                } catch(\Exception $e) {
                    Log::error("Approve transfer failed for with bag id " . $bag->id);
                    Log::error($e->getMessage());
                    event(new ErrorEvent($bag));
                }
                return;
            }
        }
        $this->delayedEvent(new ApproveTransferToArchivematicaEvent($bag), now()->addSeconds(10) );
    }

}
