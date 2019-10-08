<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ArchivematicaIngestingEvent;
use App\Events\ErrorEvent;
use App\Events\IngestCompleteEvent;
use App\Listeners\ArchivematicaClient;
use App\Listeners\ArchivematicaIngestingListener;
use App\StorageProperties;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class ArchivematicaIngestingListenerTest extends TestCase
{
    protected $bag;
    protected $zipBagFileName;
    protected $zipBagId;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->zipBagFileName = 'test.zip';
        $this->zipBagId = '007';
        $this->bag = Mockery::mock(Bag::class);
        $this->bag->shouldReceive('applyTransition')->once();
        $this->bag->shouldReceive('save')->once();
        $this->bag->shouldReceive('zipBagFileName')->once()->andReturn($this->zipBagFileName);
        $this->bag->shouldReceive('getAttribute')->once()->with('id')->andReturn($this->zipBagId);

        if(env('APP_DEBUG_SKIP_INGEST_STATUS', false)) {
            $this->markTestSkipped('Skipping test because APP_DEBUG_SKIP_INGEST_STATUS=true.');
        }
    }

    public function test_ingest_copmplete() {

        // setup
        Event::fake();
        Bus::fake();

        $this->bag->shouldReceive('zipBagFileName')->once()->andReturn($this->zipBagFileName);
        $this->bag->shouldReceive('getAttribute')->once()->with('id')->andReturn($this->zipBagId);

        $storageProperties = \Mockery::mock(StorageProperties::class);
        $storageProperties->shouldReceive('update')->once();

        $this->bag->shouldReceive('getAttribute')->once()
            ->with('storage_properties')->andReturn($storageProperties);

        $amClient = \Mockery::mock(ArchivematicaClient::class);
        $amClient->shouldReceive('getIngestStatus')
            ->once()->andReturn(json_decode(
                '{"message":"OK", "results":[{"status": "COMPLETE", "name" : "test", "uuid": "UUID"}]}'
        ));

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ErrorEvent::class);
        Event::assertDispatched(IngestCompleteEvent::class);
    }

    public function transferFailureProvider() {
        $testSet = [];
        $testSet += ["FAILED" => ["FAILED"]];
        $testSet += ["USER_INPUT" => ["USER_INPUT"]];
        return $testSet;
    }
    /**
     * @dataProvider transferFailureProvider
     */
    public function test_ingest_failure($status) {
        // setup
        Event::fake();
        Bus::fake();

        $this->bag->shouldReceive('zipBagFileName')->once()->andReturn($this->zipBagFileName);
        $this->bag->shouldReceive('getAttribute')->once()->with('id')->andReturn($this->zipBagId);

        $amClient = \Mockery::mock(ArchivematicaClient::class);
        $amClient->shouldReceive('getIngestStatus')
            ->once()->andReturn(json_decode(
                '{"message":"OK", "results":[{"status": "'.$status.'", "name" : "test"}]}'
            ));

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertNotDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }

    public function test_ingest_no_status() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaClient::class);
        $amClient->shouldReceive('getIngestStatus')
            ->once()->andReturn(json_decode(
                '{"message":"OK", "results":[]}'
            ));

        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }

    public function test_am_client_error() {
        // setup
        Event::fake();
        Bus::fake();

        $amClient = \Mockery::mock(ArchivematicaClient::class);
        $amClient->shouldReceive('getIngestStatus')
            ->once()->andReturn(json_decode(
                '{"message":"OK", "results":[]}'
        ));
        $event = new ArchivematicaIngestingEvent($this->bag);
        $listener = new ArchivematicaIngestingListener($amClient);

        //test
        $listener->handle($event);

        // assets
        Bus::assertDispatched(CallQueuedClosure::class, function ($job) {
            return true;
        });

        Event::assertNotDispatched(ErrorEvent::class);
        Event::assertNotDispatched(IngestCompleteEvent::class);
    }


}
