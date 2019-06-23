<?php

namespace App\Listeners;

use App\Events\FileUploadedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\File;
use Log;

class LogUploadedFilesListener
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
     * @param  FileUploadedEvent  $event
     * @return void
     */
    public function handle(FileUploadedEvent $event)
    {
        $file = $event->file;
        Log::info("The file ".$file->fileName." with uuid ".$file->uuid." was uploaded to bag ".$file->bag_id."." );
    }
}
