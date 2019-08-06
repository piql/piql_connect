<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Events\BagCompleteEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Bag;
use App\File;
use BagitUtil;

class CommitFilesToBagListener implements ShouldQueue
{
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
    public function handle(BagFilesEvent $event)
    {
        Log::debug("Bag commit: " . $event->bagId);
        $bag = Bag::find($event->bagId);
        $bag->status = "preparing files";
        $bag->save();

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
            event( new BagCompleteEvent($bag) );
        }
        else
        {
            Log::error("Bag ".$bag->id." failed!");
            //todo: error event
        }
    }
}
