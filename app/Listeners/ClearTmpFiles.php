<?php


namespace App\Listeners;

use App\Events\ClearTmpFilesEvent;
use App\Events\BagEvent;
use App\Events\ErrorEvent;
use BagitUtil;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Bag;
use Log;

class ClearTmpFiles implements ShouldQueue
{

    public function handle(ClearTmpFilesEvent $event)
    {
        return;
        $bag = $event->bag;
        Log::info("Clearing temporary files for bag ".$bag->zipBagFileName()." with id: ".$bag->id);
/*
        $deleteFile = function ($file) use ($bag) {
            if(!is_dir("$file")) {
                if(!unlink("$file")) {
                    Log::warning(
                        get_called_class() .
                        "Failed deleting file {$file} from bag " .
                        $bag->zipBagFileName() . " with id: " . $bag->id
                    );
                }
            }
        };
 */
        $files = $bag->files;
        foreach ($files as $file)
        {
  //          $deleteFile($fileName = $file->storagePathCompleted());
        }

   //     $deleteFile($bag->storagePathCreated());
    }

}
