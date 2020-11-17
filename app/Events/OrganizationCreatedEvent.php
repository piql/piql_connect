<?php

namespace App\Events;

use App\Organization;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;


class OrganizationCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }
}
