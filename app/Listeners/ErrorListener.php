<?php


namespace App\Listeners;

use Log;
use App\Events\ApproveTransferToArchivematicaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Bag;

class ErrorListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle Bag Error events.
     *
     * @param  object  $event
     * @return void
     */
    public function handle( $event )
    {
        $bag = $event->bag;
        if( Bag::find( $bag->id ) ) {
            Log::error( "Ingest failed for with bag with id {$bag->id} and filename {$bag->zipBagFileName()}." );
            $bag->update([ 'status' => 'error' ]);
        } else {
            Log::error( "Unknown error for ErrorListener for event {$event}" );
        }
    }
}

