<?php

namespace App\Listeners;

use App\Events\BagCompleteEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Events\StartTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Log;
use Illuminate\Support\Facades\Storage;


class SendBagToArchivematicaListener extends BagListener
{
    protected $state = "move_to_outbox";
    private $destination;

    /**
     * Create the event listener.
     *
     * @param Storage|null $storage
     */
    public function __construct(Filesystem $storage = null)
    {
        $this->destination = $storage ?? Storage::disk('am');
    }

    /**
     * Handle the event.
     *
     * @param  BagCompleteEvent  $event
     * @return void
     */
    public function _handle($event)
    {
        Log::debug("Handling BagCompleteEvent");
        $bag = $event->bag;

        $sourceFile = $bag->storagePathCreated();
        $this->destination->put( $bag->zipBagFileName(), fopen($sourceFile, 'r+'));

        event( new InitiateTransferToArchivematicaEvent( $bag ) );
    }
}
