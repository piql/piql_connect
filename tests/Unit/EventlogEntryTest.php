<?php

namespace Tests\Unit;

use App\Bag;
use App\EventLogEntry;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EventlogEntryTest extends TestCase
{

    use DatabaseTransactions;

    public function test_associating_bag_model_to_an_event_log_entry()
    {
        $user = factory(User::class)->create();
        Passport::actingAs( $user );
        $bag = factory(Bag::class)->create([
            "status" => "initiate_transfer"
        ]);
        $eventEntry = new EventLogEntry([
            'severity' => "DEBUG",
            'type' => 'DUMMY_EVENT',
            'message' => 'This is a dummy test',
        ]);
        $eventEntry->context()->associate($bag);
        $eventEntry->save();
        $eventEntry = EventLogEntry::find($eventEntry->id);
        $this->assertEquals($bag->id, $eventEntry->context->id);
    }


    public function test_not_associating_a_model_to_an_event_log_entry()
    {
        $eventEntry = new EventLogEntry([
            'severity' => "DEBUG",
            'type' => 'DUMMY_EVENT',
            'message' => 'This is a dummy test',
        ]);
        $eventEntry->save();
        $eventEntry = EventLogEntry::find($eventEntry->id);
        $this->assertEquals(null, $eventEntry->context);
    }

}
