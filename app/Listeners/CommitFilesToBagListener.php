<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use BagitUtil;

class CommitFilesToBagListener extends BagListener
{
    protected $state = 'bag_files';
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
    public function _handle($event)
    {
        $bag = $event->bag->refresh();
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
