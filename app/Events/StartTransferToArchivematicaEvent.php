<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use \App\Bag;

class StartTransferToArchivematicaEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bag;

    public function __construct(Bag $bag)
    {
        $this->bag = $bag;
    }
}
