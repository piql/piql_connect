<?php

namespace App\Listeners;

use App\Events\BagCompleteEvent;
use App\Events\ReceivedStatusFromArchivematicaEvent;
use App\Events\StartTransferToArchivematicaEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Log;
use App\Bag;


class SendBagToArchivematicaListener implements ShouldQueue
{
    private $destination;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->destination = Storage::disk('am');
    }

    /**
     * Handle the event.
     *
     * @param  BagCompleteEvent  $event
     * @return void
     */
    public function handle(BagCompleteEvent $event)
    {
        Log::debug("Handling BagCompleteEvent");
        $bag = $event->bag;
        $sourceFile = $bag->storagePathCreated();
        $this->destination->put( 'ss-location-data/'.$bag->zipBagFileName(), fopen($sourceFile, 'r+'));

        $bag->status = "processing";
        $bag->save();
        event( new StartTransferToArchivematicaEvent( $bag ) );
    }
}
