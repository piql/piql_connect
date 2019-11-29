<?php

namespace App\Events;

use App\Bag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class BagStateTransitionError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bag;

    public function __construct(Bag $bag)
    {
        $this->bag = $bag;
    }

}
