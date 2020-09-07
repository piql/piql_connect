<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\BagCompleteEvent;
use App\Events\BagFilesEvent;
use App\Events\ErrorEvent;
use App\Interfaces\MetadataWriterInterface;
use App\Listeners\CommitFilesToBagListener;
use App\Metadata;
use App\User;
use App\UserSetting;
use BagitUtil;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;
use App\Interfaces\MetadataGeneratorInterface;
use Webpatser\Uuid\Uuid;

class CommitFilesToBagListenerTest extends TestCase
{
    use DatabaseTransactions;

    private $bag;
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = $user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        Event::fake();

        $userSetting = Mockery::mock(UserSetting::class);
        $userSetting->shouldReceive('getIngestMetadataAsFileAttribute')->andReturn(true);

        $hasOne = Mockery::mock(HasOne::class);
        $hasOne->shouldReceive('first' )->andReturn($user);


        $this->bag = Mockery::mock(Bag::class);
        $this->bag->shouldReceive('canTransition')->once()->andReturn(true);
        $this->bag->shouldReceive('applyTransition')->once()->andReturnSelf();
        $this->bag->shouldReceive('save')->once()->andReturnSelf();
        $this->bag->shouldReceive( 'owner' )
            ->andReturn( $hasOne );


    }

    public function test_given_a_bag_with_files_when_commiting_files_it_dispatches_bag_complete()
    {
        $bagitUtil = Mockery::mock( BagitUtil::class );
        $bagitUtil->shouldReceive( 'addFile' )->once();
        $bagitUtil
            ->shouldReceive( 'createBag' )
            ->once()
            ->andReturn( true );

        $bag = $this->bag;
        $bag->shouldReceive( 'storagePathCreated' )
             ->once()->andReturn( "" );

        $file = new \App\File([
            'bag_id' => Uuid::generate()->string,
            'filename' => "test",
            'uuid' => Uuid::generate()->string
        ]);
        $file->filesize = 1;
        $file->save();

        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
            "uuid" => Uuid::generate()->string,
            "metadata" => [],
        ]);
        $metadata->parent()->associate($file);
        $metadata->owner()->associate($this->user);
        $metadata->save();

        $bag->shouldReceive( 'getAttribute' )
            ->with( 'files' )
            ->twice()
            ->andReturn( collect([$file] ));

        $metadataGenerator = Mockery::mock( MetadataGeneratorInterface::class, function( $mock ) use ($file){
            $mock->shouldReceive('createMetadataWriter')
                ->once()
                ->andReturn( Mockery::mock( MetadataWriterInterface::class, function( $mock ) use ($file) {
                    $mock->shouldReceive('write')->times(1)->with(Mockery::on(function($argument) use ($file) {
                        $this->assertArrayHasKey("object", $argument);
                        $this->assertEquals(CommitFilesToBagListener::FILE_OBJECT_PATH.$file->filename, $argument["object"]);
                        return true;
                    }))->andReturn(true);
                    $mock->shouldReceive('close')->once()->andReturn(true);
                }));
        });

        $event = new BagFilesEvent( $bag );
        $listener = new CommitFilesToBagListener( $bagitUtil, $metadataGenerator );
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

        $metadataGenerator = Mockery::mock( MetadataGeneratorInterface::class, function( $mock ) {
            $mock->shouldReceive('createMetadataWriter')->never();
        });

        $event = new BagFilesEvent($bag);
        $listener = new CommitFilesToBagListener($bagitUtil, $metadataGenerator);
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

        $metadataGenerator = Mockery::mock( MetadataGeneratorInterface::class, function( $mock ) {
            $mock->shouldReceive('createMetadataWriter')
                ->once()
                ->andReturn( Mockery::mock( MetadataWriterInterface::class, function( $mock ) {
                    $mock->shouldReceive('close')->once()->andReturn(true);
                }));
        });

        $event = new BagFilesEvent( $bag );
        $listener = new CommitFilesToBagListener( $bagitUtil, $metadataGenerator );
        $listener->handle( $event );

        Event::assertNotDispatched( BagCompleteEvent::class );
        Event::assertDispatched( ErrorEvent::class) ;
    }
}
