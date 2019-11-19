<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ApproveTransferToArchivematicaEvent;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Listeners\ArchivematicaClient;
use App\Listeners\InitiateTransferToArchivematicaListener;
use App\Listeners\SendBagToArchivematicaListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class SendBagToArchivematicaListenerTest extends TestCase
{
    protected $bag;
    protected $storage;
    protected $dstFile;
    protected $srcFile;
    protected $fileContent;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->storage = Storage::fake();
        $this->dstFile = 'dst/test.zip';
        $this->srcFile = 'src/test.zip';
        $this->bag = Mockery::mock(Bag::class);
        $this->bag->shouldReceive('applyTransition')->once();
        $this->bag->shouldReceive('save')->once();
        $this->bag->shouldReceive('storagePathCreated')->once()->andReturn($this->storage->path($this->srcFile));
        $this->bag->shouldReceive('zipBagFileName')->once()->andReturn($this->dstFile);

        $this->fileContent =  "FileId: ".Uuid::generate();;
        $this->storage->put($this->srcFile, $this->fileContent);
    }

    public function test_initiate_transfer_success() {

        // setup
        Event::fake();

        $event = new BagCompleteEvent($this->bag);
        $listener = new SendBagToArchivematicaListener($this->storage);

        //test
        $listener->handle($event);

        // assets
        $this->assertStringEqualsFile($this->storage->path($this->dstFile), $this->fileContent);
        Event::assertDispatched(InitiateTransferToArchivematicaEvent::class);
    }
}