<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\BagCompleteEvent;
use App\Events\BagFilesEvent;
use App\Events\ErrorEvent;
use App\Listeners\CommitFilesToBagListener;
use BagitUtil;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class CommitFilesToBagListenerTest extends TestCase
{
    private $bag;

    public function setUp() : void
    {
        parent::setUp();
        Event::fake();

        $this->bag = Mockery::mock(Bag::class);
        $this->bag->shouldReceive('canTransition')->once()->andReturn(true);
        $this->bag->shouldReceive('applyTransition')->once()->andReturnSelf();
        $this->bag->shouldReceive('save')->once()->andReturnSelf();
    }

    public function test_given_a_bag_with_files_when_commiting_files_it_dispatches_bag_complete()
    {
        $bagitUtil = Mockery::mock( BagitUtil::class );
        $bagitUtil->shouldReceive( 'addFile' )->twice();
        $bagitUtil
            ->shouldReceive( 'createBag' )
            ->once()
            ->andReturn( true );

        $bag = $this->bag;
        $bag->shouldReceive( 'storagePathCreated' )
             ->once()->andReturn( "" );
        $bag->shouldReceive( 'getAttribute' )
            ->with( 'files' )
            ->twice()
            ->andReturn( collect([new \App\File, new \App\File] ));

        $event = new BagFilesEvent( $bag );
        $listener = new CommitFilesToBagListener( $bagitUtil );
        $listener->handle( $event );

        Event::assertDispatched( BagCompleteEvent::class );
        Event::assertNotDispatched( ErrorEvent::class );
    }

    public function test_given_a_bag_without_files_when_commiting_files_it_dispatches_error_and_not_bag_complete()
    {
        $bag = $this->bag;
        $bag->shouldReceive( 'getAttribute' )->with( 'files' )->twice()
            ->andReturn( collect([]) );
        $bag->shouldReceive( 'getAttribute' )->with( 'id' )->once();

        $bagitUtil = Mockery::mock(BagitUtil::class);
        $bagitUtil->shouldReceive('addFile')->never();
        $bagitUtil->shouldReceive('createBag')->never();

        $event = new BagFilesEvent($bag);
        $listener = new CommitFilesToBagListener($bagitUtil);
        $listener->handle($event);

        Event::assertNotDispatched( BagCompleteEvent::class );
        Event::assertDispatched( ErrorEvent::class );
    }

    public function test_given_a_broken_bag_when_commiting_files_it_dispatches_error_and_not_bag_complete()
    {
        $bagitUtil = Mockery::mock(BagitUtil::class);
        $bagitUtil->shouldReceive('addFile')->twice();
        $bagitUtil->shouldReceive('createBag')->once()->andReturn(false);

        $bag = $this->bag;
        $bag->shouldReceive('getAttribute')
            ->with('files')
            ->twice()
            ->andReturn( collect([new \App\File, new \App\File] ));
        $bag->shouldReceive('storagePathCreated')->once();
        $bag->shouldReceive('getAttribute')->with('id')->once();

        $event = new BagFilesEvent( $bag );
        $listener = new CommitFilesToBagListener( $bagitUtil );
        $listener->handle( $event );

        Event::assertNotDispatched( BagCompleteEvent::class );
        Event::assertDispatched( ErrorEvent::class) ;
    }
}
