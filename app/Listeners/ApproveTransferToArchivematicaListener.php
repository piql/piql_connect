<?php


namespace App\Listeners;


use App\Events\ArchivematicaTransferringEvent;
use Log;

class ApproveTransferToArchivematicaListener extends BagListener
{
    protected $state = "approve_transfer";
    private $amClient;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->amClient = new ArchivematicaClient();
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

        $waitingForApprove = true;
        while($waitingForApprove)
        {
            $currentTransferStatuses = $this->amClient->getTransferStatus();
            foreach($currentTransferStatuses->results as $status)
            {
                if($status->name == $bag->zipBagFileName())
                {
                    $this->amClient->approveTransfer($bag->id);
                    event(new ArchivematicaTransferringEvent($bag));
                    $waitingForApprove = false;
                    break;
                }
            }
            sleep(2);
        }

    }

}
