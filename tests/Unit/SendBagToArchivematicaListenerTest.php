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
    public function test_initiate_transfer_success()
    {
        Event::fake();

        $storage = Storage::fake();
        $dstFile = 'dst/test.zip';
        $srcFile = 'src/test.zip';
        $bag = Mockery::mock(Bag::class);
        $bag->shouldReceive('canTransition')->once()->andReturn( true );;
        $bag->shouldReceive('applyTransition')->once()->andReturnSelf();
        $bag->shouldReceive('save')->once();
        $bag->shouldReceive('storagePathCreated')->once()->andReturn($storage->path($srcFile));
        $bag->shouldReceive('zipBagFileName')->once()->andReturn($dstFile);
        $storage->put($srcFile, "");

        $event = new BagCompleteEvent($bag);
        $listener = new SendBagToArchivematicaListener($storage);
        $listener->handle($event);

        Event::assertDispatched(InitiateTransferToArchivematicaEvent::class);
    }
}
