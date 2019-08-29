<?php


namespace App\Listeners;

use App\Events\BagEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class BagListener implements ShouldQueue
{
    protected $state = "";

    public function handle($event)
    {
        $bag = $event->bag;
        try {
            $bag->applyTransition($this->state);
        } catch (BagTransitionException $e) {
            Log::warning("Caught an exception changing bag state. Exception: {$e}");
            return;
        }

        $this->_handle($event);
    }

    public function _handle($event)
    {

    }

    public function delayedEvent($event, $when, $listenerClassName = null)
    {
        if(!$listenerClassName)
        {
            $listenerClassName = get_called_class();
        }
        dispatch(function () use ($listenerClassName, $event) {
            $listener = new $listenerClassName();
            $listener->handle($event);
        })->delay($when);
    }
}
