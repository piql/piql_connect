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
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BagFilesEvent  $event
     * @return void
     */
    public function _handle($event)
    {
        $bag = $event->bag;
        $files = $bag->files;

        $bagIt = new BagitUtil();
        // Create a bag
        foreach ($files as $file)
        {
            $bagIt->addFile($file->storagePathCompleted(), $file->filename);
        }

        $result = $bagIt->createBag($bag->storagePathCreated());

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
