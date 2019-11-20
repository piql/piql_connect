<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use BagitUtil;

class CommitFilesToBagListener implements ShouldQueue
{
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
    public function handle($event)
    {
        $bag = $event->bag->refresh();
        $bag->applyTransition( "bag_files" );
        $bag->save();

        $files = $bag->files;

        // Use bagIt to create a zipped bag on disk
        if(!$bag->files->count())
        {
            Log::error("Empty bag(".$bag->id.")");
            event( new ErrorEvent ($bag) );
            return;
        }

        foreach ($files as $file)
        {
            $this->bagIt->addFile($file->storagePathCompleted(), $file->filename);
        }

        $result = $this->bagIt->createBag($bag->storagePathCreated());

        if($result)
        {
            Log::info("Bag ".$bag->id." built ok!");
            event( new BagCompleteEvent ($bag) );
        }
        else
        {
            Log::error("Bag ".$bag->id." failed!");
            event( new ErrorEvent($bag) );
        }
    }
}
