<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use \App\Bag;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $severity;
    public $owner;
    public $properties;

    public function __construct($severity, $owner, array $properties)
    {
        $this->properties = $properties;
        $this->severity = $severity;
        $this->owner = $owner;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {

        return new PrivateChannel('User.'.$this->owner.'.Events');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'severity' => $this->severity,
            'properties' => $this->properties
        ];
    }

    public function broadcastAs()
    {
        return $this->severity;
    }

}
