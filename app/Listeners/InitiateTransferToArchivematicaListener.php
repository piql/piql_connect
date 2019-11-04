<?php


namespace App\Listeners;


use App\ArchivematicaService;
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
     * @param ArchivematicaClient|null $amClient
     */
    public function __construct(ArchivematicaClient $amClient = null)
    {
        $this->amClient = $amClient ?? new ArchivematicaClient( new ArchivematicaServiceConnection( ArchivematicaService::first()));
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

        $response = $this->amClient->initiateTransfer($bag->name, $bag->uuid, $bag->zipBagFileName());

        if($response->statusCode != 200) {
            $message = " initiate transfer failed with error code " . $response->statusCode;

            if (isset($response->content->error) && ($response->content->error == true)) {
                $message += " and error message: " . $response->content->message;
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        event(new ApproveTransferToArchivematicaEvent($bag));
    }
}
