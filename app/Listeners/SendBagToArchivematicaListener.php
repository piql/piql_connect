<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Events\BagCompleteEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Events\StartTransferToArchivematicaEvent;
use App\Traits\BagOperations;
use Log;

class SendBagToArchivematicaListener implements ShouldQueue
{
    use BagOperations;

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
    public function handle( $event )
    {
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag, "move_to_outbox" ) ){
            Log::error(" SendBagToArchivematicaListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state '{move_to_outbox}'" );
            return;
        }

        $sourceFile = $bag->storagePathCreated();
        $this->destination->put( $bag->zipBagFileName(), fopen($sourceFile, 'r+'));

        event( new InitiateTransferToArchivematicaEvent( $bag ) );
    }
}
