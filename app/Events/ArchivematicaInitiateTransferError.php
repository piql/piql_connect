<?php


namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArchivematicaInitiateTransferError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bag;
    public $message;

    /**
     * ConnectionException constructor.
     * @param $bag
     * @param string $message
     */
    public function __construct($bag, string $message)
    {
        $this->bag = $bag;
        $this->message = $message;
    }
}
