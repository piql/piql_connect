<?php


namespace App\Listeners;


use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ErrorEvent;
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
        Log::info("Handling InitiateTransferToArchivematicaEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);

        try{
            $this->amClient->initiateTransfer($bag->id);
        } catch(\Exception $e){
            Log::error(" initiate transfer failed with  ".$e->getMessage());
            event(new ErrorEvent($bag));
            return;
        }


        event(new ApproveTransferToArchivematicaEvent($bag));
    }
}
