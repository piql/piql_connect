<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Interfaces\PreProcessBagInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Traits\BagOperations;

class PreProcessBagListener implements ShouldQueue
{
    use BagOperations;

    protected $preProcessBag;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PreProcessBagInterface $preProcessBag)
    {
        $this->preProcessBag = $preProcessBag;
    }

    /**
     * Handle the event.
     *
     * @param  BagFilesEvent  $event
     * @return void
     */
    public function handle( $event )
    {
        collect($this->preProcessBag->process( $event->bag))->each(function($bag) {
            event( new BagFilesEvent($bag) );
        });

    }
}
