<?php

namespace App\Listeners;

use App\Events\BagCompleteEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Events\StartTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Illuminate\Support\Facades\Storage;


class SendBagToArchivematicaListener extends BagListener
{
    protected $state = "move_to_outbox";
    private $destination;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->destination = Storage::disk('am');
    }

    /**
     * Handle the event.
     *
     * @param  BagCompleteEvent  $even     * @return void
     */
    public function _handle($event)
    {
        Log::debug("Handling BagCompleteEvent");
        $bag = $event->bag;

        $sourceFile = $bag->storagePathCreated();
        $destinationFile = storage_path('am/ss-location-data')."/".$bag->zipBagFileName();
        Log::debug("Uploading source file ".$sourceFile." to ".$destinationFile);
        \File::copy($sourceFile, $destinationFile);
        //$this->destination->put( $bag->zipBagFileName(), fopen($sourceFile, 'r+'));
        event( new InitiateTransferToArchivematicaEvent( $bag ) );
    }
}
