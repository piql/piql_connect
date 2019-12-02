<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\ArchivematicaGetTransferStatusError;
use App\Events\ArchivematicaIngestingEvent;
use App\Events\ArchivematicaTransferError;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\ConnectionError;
use App\Events\ErrorEvent;
use App\File;
use App\Listeners\ApproveTransferToArchivematicaListener;
use App\Interfaces\ArchivematicaDashboardClientInterface;
use App\Listeners\ArchivematicaTransferringListener;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class ArchivematicaTransferringListenerTest extends TestCase
{

    use DatabaseTransactions;
    protected $bag;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = factory(User::class)->create();
        Passport::actingAs( $user );
        $bag = factory(Bag::class)->create([
            "owner" => $user->id,
            "status" => "transferring"
        ]);

        factory(File::class, 1)->create()->each(function($file) use ($bag) {
            $file->bag_id = $bag->id;
            $file->save();
        });

        $this->bag = $bag->fresh();

    }

    public function test_transfer_copmplete() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getTransferStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched transfer status successfully.',
                    'name' => $this->bag->BagFileNameNoExt(),
                    'sip_uuid' => 'ff29e8b0-0bba-11ea-9a8c-1500a571ff88',
                    'type' => 'zipped bag',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                    'status' => 'COMPLETE'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ArchivematicaTransferringEvent($this->bag);
        $listener = new ArchivematicaTransferringListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetTransferStatusError::class);
        Event::assertNotDispatched(ArchivematicaTransferError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertNotDispatched(ErrorEvent::class);
        Event::assertDispatched(ArchivematicaIngestingEvent::class);
    }

    public function test_get_transfer_status_with_http_error_400() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getTransferStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                ],
                'statusCode' => 400,
            ]
        );

        $event = new ArchivematicaTransferringEvent($this->bag);
        $listener = new ArchivematicaTransferringListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetTransferStatusError::class);
        Event::assertNotDispatched(ArchivematicaTransferError::class);
        Event::assertDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(ArchivematicaIngestingEvent::class);
    }

    public function test_get_transfer_status_with_ERROR_status() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getTransferStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched transfer status successfully.',
                    'name' => $this->bag->BagFileNameNoExt(),
                    'type' => 'zipped bag',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                    'status' => 'FAILED'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ArchivematicaTransferringEvent($this->bag);
        $listener = new ArchivematicaTransferringListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetTransferStatusError::class);
        Event::assertDispatched(ArchivematicaTransferError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(ArchivematicaIngestingEvent::class);
    }

    public function test_get_transfer_status_with_USER_INPUT_status() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getTransferStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched transfer status successfully.',
                    'name' => $this->bag->BagFileNameNoExt(),
                    'type' => 'zipped bag',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                    'status' => 'USER_INPUT'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ArchivematicaTransferringEvent($this->bag);
        $listener = new ArchivematicaTransferringListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetTransferStatusError::class);
        Event::assertDispatched(ArchivematicaTransferError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(ArchivematicaIngestingEvent::class);
    }

}
