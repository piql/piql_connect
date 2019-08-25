<?php


namespace App\Listeners;

use Log;
use App\Events\ApproveTransferToArchivematicaEvent;

class ErrorListener extends BagListener
{
    protected $state = "error";
    private $amClient;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->amClient = new ArchivematicaClient();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function _handle($event)
    {
        $bag = $event->bag;
        Log::error("Ingesting bag ".$bag->zipBagFileName()." with id: ".$bag->id) . "failed!";
    }
}
{

}
