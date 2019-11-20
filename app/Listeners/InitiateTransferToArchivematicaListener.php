<?php


namespace App\Listeners;


use App\ArchivematicaService;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ErrorEvent;
use Log;

class InitiateTransferToArchivematicaListener implements ShouldQueue
{
    private $amClient;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaClient|null $amClient
     */
    public function __construct(ArchivematicaClient $amClient = null)
    {
        $this->amClient = $amClient ?? new ArchivematicaClient();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $bag = $event->bag->refresh();
        Log::info("Handling InitiateTransferToArchivematicaEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);
        $bag->applyTransition( "initiate_transfer" );
        $bag->save();

        $response = $this->amClient->initiateTransfer($bag->name, $bag->uuid, $bag->zipBagFileName());

        if($response->statusCode != 200) {
            $message = "Initiate transfer failed with error code " . $response->statusCode;

            if (isset($response->contents->error) && ($response->contents->error == true)) {
                $message .= " and error message: " . $response->contents->message;
            }

            Log::error($message);
            event(new ErrorEvent($bag));
            return;
        }

        event(new ApproveTransferToArchivematicaEvent( $bag ));
    }
}
