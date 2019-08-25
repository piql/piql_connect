<?php


namespace App\Listeners;


use App\Events\ApproveTransferToArchivematicaEvent;
use Log;

class InitiateTransferToArchivematicaListener extends BagListener
{
    protected $state = "initiate_transfer";
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
        Log::info("Handling StartTransferToArchivematicaEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        $this->amClient->initiateTransfer($bag->id);
        event(new ApproveTransferToArchivematicaEvent($bag));
    }
}
