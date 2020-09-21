<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class FileUploadedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file;
    public $owner;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( \App\File $file, \App\Auth\User $owner )
    {
        $this->file = $file;
        $this->owner = $owner;
    }
}
