<?php

namespace Tests\Unit;

use App\Events\OrganizationCreatedEvent;
use App\Organization;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp() : void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @covers test create a basic organization with an uuid and name
     */
    public function test_create_a_basic_organization() {
        // prevent firing events
        Event::fake();

        $org = factory(Organization::class)->create();
        $org = Organization::findOrFail($org->id);
        $this->assertTrue(strlen($org->name) > 0,
            "Organization doesn't have a valid name");
        $this->assertStringMatchesFormat("%x-%x-%x-%x-%x", $org->uuid,
            "Organization doesn't have a valid uuid");
    }

    /**
     * @covers test create an organization and make sure an OrganizationCreatedEvent is sent
     */
    public function test_create_organization_and_validate_OrganizationCreatedEvent_is_sent() {
        $event = Event::fake();

        $org = factory(Organization::class)->create();
        $event->assertDispatched(OrganizationCreatedEvent::class,
            function(OrganizationCreatedEvent $event) use ($org) {
                return $org->uuid == $event->organization->uuid;
            });
    }
}
