<?php

namespace App\Listeners;

use App\Events\ProcessFilesEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Bag;
use App\File;
use BagitUtil;

class CommitFilesToBagListener
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
     * @param  ProcessFilesEvent  $event
     * @return void
     */
    public function handle(ProcessFilesEvent $event)
    {
        Log::debug("CommitFilesToBagListener handler");

        $bag = Bag::find($event->bagId);
        $bag->status = "bagging";
        $bag->save();

        Log::debug("Bag commit: " . $bag->id);

        $bagit = new BagitUtil;

        $files = $bag->files;

        // Create bag output dir
        $bagOuputDir = dirname($bag->storagePathCreated());
        if (!is_dir($bagOuputDir))
        {
            if (!mkdir($bagOuputDir))
            {
                Log::error("Failed to create bag output dir: " . $bagOuputDir);
                // \todo Error event
            }
        }

        // Create a bag
        $bagPath = $bag->storagePathCreated();
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

        // Change bag status
        $bag->status = 'processing';
        $bag->save();

        Log::debug("Bag created.");

    }
}
