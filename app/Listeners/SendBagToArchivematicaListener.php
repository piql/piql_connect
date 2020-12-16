<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Events\BagCompleteEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Events\StartTransferToArchivematicaEvent;
use App\Traits\BagOperations;
use Log;

class SendBagToArchivematicaListener implements ShouldQueue
{
    use BagOperations;

    private $storageTo;
    private $storageFrom;

    /**
     * Create the event listener.
     *
     * @param Filesystem|null $storageTo
     * @param Filesystem|null $storageFrom
     */
    public function __construct(Filesystem $storageFrom = null, Filesystem $storageTo = null)
    {
        $this->storageTo = $storageTo ?? Storage::disk('am');
        $this->storageFrom = $storageFrom ?? Storage::disk('bags');
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

        $directories = [];
        if(File::isDirectory($this->storageFrom->getAdapter()->applyPathPrefix($bag->zipBagFileName()))) {
            $files = $this->storageFrom->allFiles($bag->zipBagFileName());
            $directories[] = $bag->zipBagFileName();
        } else {
            $files[] = $bag->zipBagFileName();
        }

        // create directories
        $directories = array_merge($directories, $this->storageFrom->allDirectories($bag->zipBagFileName()));
        foreach ($directories as $directory) {
            if($this->storageTo->makeDirectory($directory)) {

            }
        }

        // copy the files
        foreach ($files as $file) {
            if(File::copy(
                $this->storageFrom->getAdapter()->applyPathPrefix($file),
                $this->storageTo->getAdapter()->applyPathPrefix($file)
            )) {

            }
        }
        event( new InitiateTransferToArchivematicaEvent( $bag ) );
    }
}
