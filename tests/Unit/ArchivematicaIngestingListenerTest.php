<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ArchivematicaGetIngestStatusError;
use App\Events\ArchivematicaIngestError;
use App\Events\ArchivematicaIngestingEvent;
use App\Events\ConnectionError;
use App\Events\ErrorEvent;
use App\Events\IngestCompleteEvent;
use App\File;
use App\Listeners\ArchivematicaIngestingListener;
use App\Interfaces\ArchivematicaDashboardClientInterface;
use App\StorageProperties;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;
use Laravel\Passport\Passport;

class ArchivematicaIngestingListenerTest extends TestCase
{

    use DatabaseTransactions;
    protected $bag;
    protected $zipBagFileName;
    protected $zipBagId;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        if(env('APP_DEBUG_SKIP_INGEST_STATUS', false)) {
            $this->markTestSkipped('Skipping test because APP_DEBUG_SKIP_INGEST_STATUS=true.');
        }

        $user = factory(User::class)->create();
        Passport::actingAs( $user );

        $bag = factory(Bag::class)->create([
            "status" => "ingesting"
        ]);

        factory(File::class, 1)->create()->each(function($file) use ($bag) {
            $file->bag_id = $bag->id;
            $file->save();
        });

        $this->bag = $bag->fresh();

    }

    public function test_get_ingest_status_with_status_COMPLETE() {

        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched ingest status successfully.',
                    'name' => $this->bag->BagFileNameNoExt(),
                    'type' => 'zipped bag',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                    'status' => 'COMPLETE'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });
        Event::assertNotDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertNotDispatched(ArchivematicaIngestError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertNotDispatched(ErrorEvent::class);
        Event::assertDispatched(IngestCompleteEvent::class);
    }

    public function test_get_ingest_status_with_http_error_400() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Approval failed.',
                    'error' => 'true'
                ],
                'statusCode' => 400,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertNotDispatched(ArchivematicaIngestError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertNotDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }

    public function test_get_ingest_status_with_http_error_400_and_no_error_message() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                ],
                'statusCode' => 400,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertNotDispatched(ArchivematicaIngestError::class);
        Event::assertDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }

    public function test_get_ingest_status_with_http_error_404() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Approval failed.',
                    'error' => 'true'
                ],
                'statusCode' => 404,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertNotDispatched(ArchivematicaIngestError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }

    public function test_get_ingest_status_with_http_error_400_without_error_message() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                ],
                'statusCode' => 400,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertNotDispatched(ArchivematicaIngestError::class);
        Event::assertDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }

    public function test_get_ingest_status_with_ERROR_status() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched ingest status successfully.',
                    'name' => $this->bag->BagFileNameNoExt(),
                    'type' => 'zipped bag',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                    'status' => 'FAILED'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertDispatched(ArchivematicaIngestError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }



    public function test_get_ingest_status_with_USER_INPUT_status() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched ingest status successfully.',
                    'name' => $this->bag->BagFileNameNoExt(),
                    'type' => 'zipped bag',
                    'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                    'status' => 'USER_INPUT'
                ],
                'statusCode' => 200,
            ]
        );

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ArchivematicaGetIngestStatusError::class);
        Event::assertDispatched(ArchivematicaIngestError::class);
        Event::assertNotDispatched(ConnectionError::class);
        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }
}
