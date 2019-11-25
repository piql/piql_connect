<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ClearTmpFilesEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bag;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bag)
    {
        $this->bag = $bag;
    }

}
