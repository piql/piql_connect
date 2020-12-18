<?php


namespace App\Listeners;

use App\Events\ClearTmpFilesEvent;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Log;

class ClearTmpFiles implements ShouldQueue
{
    private $storage;

    public function __construct(Filesystem $storage)
    {
        $this->storage = $storage;
    }

    public function handle(ClearTmpFilesEvent $event)
    {
        $bag = $event->bag;

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

        $files = $bag->files;
        foreach ($files as $file)
        {
            $deleteFile($fileName = $file->storagePathCompleted());
        }

        $deleteFile($bag->storagePathCreated());

        if(File::isDirectory($this->storage->getAdapter()->applyPathPrefix($bag->zipBagFileName()))) {
            if ($this->storage->deleteDirectory($bag->zipBagFileName()) === false) {
                Log::warning("Failed to delete {$bag->zipBagFileName()} from storage" . $this->storage->path(""));
            }
        } else {
            if ($this->storage->delete($bag->zipBagFileName()) === false) {
                Log::warning("Failed to delete {$bag->zipBagFileName()} from storage" . $this->storage->path(""));
            }
        }
    }

}
