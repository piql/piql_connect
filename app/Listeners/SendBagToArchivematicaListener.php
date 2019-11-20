<?php

namespace App\Listeners;

use App\Events\BagCompleteEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Events\StartTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Log;

class SendBagToArchivematicaListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @param Storage|null $storage
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  BagCompleteEvent  $event
     * @return void
     */
    public function handle($event)
    {
        $bag = $event->bag->refresh();
        $bag->applyTransition( "move_to_outbox" );
        $bag->save();

        Log::debug("Handling BagCompleteEvent for bag {$bag->id} in state {$bag}");

        $sourceFile = $bag->storagePathCreated();
//        Log::debug("SendBagToArchivematicaListener will write to path: {$destination}");
//        $destination->put( $bag->zipBagFileName(), fopen($sourceFile, 'r+'));
        $destination = Storage::disk( 'am' )->path( $bag->zipBagFileName() );
        Log::debug("Copying bag {$sourceFile} to {$destination}");
        //Storage::disk('local')->copy( $sourceFile, $destination );
        //        File::copy( storage_path()."/storage/bags/".$bag->zipBagFileName(), storage_path()."/storage/ss-location-data/".$bag->zipBagFileName() );
        Log::debug("Copying complete");

        event( new InitiateTransferToArchivematicaEvent( $bag ) );
    }
}
