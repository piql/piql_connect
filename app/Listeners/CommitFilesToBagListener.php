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
        Log::debug("CommitFilesToBagListener handler");

        $bag = Bag::find($event->bagId);
        $bag->status = "preparing files";
        $bag->save();

        Log::debug("Bag commit: " . $bag->id);

        $bagit = new BagitUtil;

        $files = $bag->files;

        // Create bag output dir
        $bagOutputDir = dirname($bag->storagePathCreated());
        Log::debug("Bag output dir: ".$bagOutputDir);
/*        if (!is_dir($bagOuputDir))
        {
            if (!mkdir($bagOuputDir))
            {
                Log::error("Failed to create bag output dir: " . $bagOuputDir);
                // \todo Error event
            }
        }
 */
        // Create a bag
        $bagPath = $bag->storagePathCreated();
        Log::debug("Bag path: ".$bagPath);
        foreach ($files as $file)
        {
            $filePath = $file->storagePathCompleted();
            $bagit->addFile($filePath, $file->filename);
        }
        if (!$bagit->createBag($bagPath))
        {
            Log::error("Failed to create bag: " . $bagit->errorMessage());
            // \todo Error event
        }


        Log::debug("Bag created.");
        event( new BagCompleteEvent($bag) );
    }
}
