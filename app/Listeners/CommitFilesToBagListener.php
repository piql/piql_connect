<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use BagitUtil;
use App\Traits\BagOperations;

class CommitFilesToBagListener implements ShouldQueue
{
    use BagOperations;

    protected $bagIt;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BagitUtil $bagIt = null)
    {
        $this->bagIt = $bagIt ?? new BagitUtil();
    }

    /**
     * Handle the event.
     *
     * @param  BagFilesEvent  $event
     * @return void
     */
    public function handle( $event )
    {
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag, "bag_files" ) ){
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state 'bag_files'" );
            return;
        }

        $files = $bag->files;

        // Create a bag
        if(!$bag->files->count())
        {
            Log::error("Empty bag(".$bag->id.")");
            event( new ErrorEvent ($bag) );
            return;
        }

        foreach ($files as $file)
        {
            if( ($file->filename === "metadata.csv") && $bag->owner()->settings->getIngestMetadataAsFileAttribute() )
                $this->bagIt->addMetadataFile($file->storagePathCompleted(), $file->filename);
            else
                $this->bagIt->addFile($file->storagePathCompleted(), $file->filename);
        }

        $result = $this->bagIt->createBag($bag->storagePathCreated());

        if($result)
        {
            event( new BagCompleteEvent ($bag) );
        }
        else
        {
            Log::error("Bag ".$bag->id." failed!");
            event( new ErrorEvent($bag) );
        }
    }
}
