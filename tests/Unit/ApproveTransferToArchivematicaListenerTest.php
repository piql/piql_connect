<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\ErrorEvent;
use App\File;
use App\Listeners\ApproveTransferToArchivematicaListener;
use App\Listeners\ArchivematicaClient;
use App\User;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use PhpParser\Node\Expr\Closure;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ApproveTransferToArchivematicaListenerTest extends TestCase
{
    use DatabaseTransactions;
    private $bag;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = factory(User::class)->create();
        $bag = factory(Bag::class)->create([
            "owner" => $user->id,
            "status" => "initiate_transfer"
        ]);

        factory(File::class, 1)->create()->each(function($file) use ($bag) {
            $file->bag_id = $bag->id;
            $file->save();
        });

        $this->bag = $bag->fresh();
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function test_approve_transfer_success() {
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaClient::class);

        $amClient->shouldReceive('getUnapprovedList')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched unapproved transfers successfully.',
                    'results' =>  [
                        (object) [
                            'directory' => $this->bag->zipBagFileName(),
                            'type' => 'zipped bag',
                            'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d'
                        ]
                    ]
                ],
                'statusCode' => 200,
            ]
        );

        $amClient->shouldReceive('approveTransfer')->once()->with(
            $this->bag->zipBagFileName()
        )->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Approval successful.',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ApproveTransferToArchivematicaEvent($this->bag);
        $listener = new ApproveTransferToArchivematicaListener($amClient);
        $listener->handle($event);

        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertDispatched(ArchivematicaTransferringEvent::class);
        Event::assertNotDispatched(ApproveTransferToArchivematicaEvent::class);
    }

    public function test_unapproved_list_is_empty_error() {
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaClient::class);

        $amClient->shouldReceive('getUnapprovedList')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched unapproved transfers successfully.',
                    'results' =>  [
                    ]
                ],
                'statusCode' => 200,
            ]
        );

        $amClient->shouldReceive('approveTransfer')->never();

        $event = new ApproveTransferToArchivematicaEvent($this->bag);
        $listener = new ApproveTransferToArchivematicaListener($amClient);
        $listener->handle($event);


        Bus::assertDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaTransferringEvent::class);
        Event::assertNotDispatched(ErrorEvent::class);
    }

    public function test_get_unapproved_list_fails() {
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaClient::class);

        $amClient->shouldReceive('getUnapprovedList')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched unapproved transfers successfully.',
                    'results' =>  [
                    ]
                ],
                'statusCode' => 400,
            ]
        );

        $amClient->shouldReceive('approveTransfer')->never();

        $event = new ApproveTransferToArchivematicaEvent($this->bag);
        $listener = new ApproveTransferToArchivematicaListener($amClient);
        $listener->handle($event);


        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaTransferringEvent::class);
        Event::assertDispatched(ErrorEvent::class);
    }

    public function test_approve_transfer_fails() {
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaClient::class);

        $amClient->shouldReceive('getUnapprovedList')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched unapproved transfers successfully.',
                    'results' =>  [
                        (object) [
                            'directory' => $this->bag->zipBagFileName(),
                            'type' => 'zipped bag',
                            'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d'
                        ]
                    ]
                ],
                'statusCode' => 200,
            ]
        );

        $amClient->shouldReceive('approveTransfer')->once()->with(
            $this->bag->zipBagFileName()
        )->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Approval failed.',
                    'error' => 'true'
                ],
                'statusCode' => 400,
            ]
        );

        $event = new ApproveTransferToArchivematicaEvent($this->bag);
        $listener =new ApproveTransferToArchivematicaListener($amClient);
        $listener->handle($event);

        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaTransferringEvent::class);
        Event::assertDispatched(ErrorEvent::class);
    }

}