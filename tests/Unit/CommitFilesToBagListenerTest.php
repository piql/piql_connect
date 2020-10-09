<?php

namespace Tests\Unit;

use App\Account;
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
use App\MetadataPath;

class CommitFilesToBagListenerTest extends TestCase
{
    use DatabaseTransactions;

    private $bag;
    private $user;
    private $account;

    public function setUp() : void
    {
        parent::setUp();
        $this->account = factory(Account::class)->create();
        $this->user = $user = factory(User::class)->create();
        $this->user->account()->associate( $this->account );
        Passport::actingAs( $this->user );


        $userSetting = Mockery::mock(UserSetting::class);
        $userSetting->shouldReceive('getIngestMetadataAsFileAttribute')->andReturn(true);

        $hasOne = Mockery::mock(HasOne::class);
        $hasOne->shouldReceive('first' )->andReturn($user);

        $this->bag = Bag::create([
            'name' => "Test bag",
            'status' => "closed",
            'owner' => $this->user->id
        ]);


        Event::fake();

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

        $file = new \App\File([
            'bag_id' => $bag->id,
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


        $metadataGenerator = Mockery::mock( MetadataGeneratorInterface::class, function( $mock ) use ($file){
            $mock->shouldReceive('createMetadataWriter')
                ->once()
                ->andReturn( Mockery::mock( MetadataWriterInterface::class, function( $mock ) use ($file) {

                    // TODO: review this when metadata ingest is up and running again
                    $mock->shouldReceive('write')->times(MetadataPath::count())->with(Mockery::on(function ($argument) use ($file) {
                        $this->assertArrayHasKey("object", $argument);
                        switch ($argument['object']) {
                            case MetadataPath::ACCOUNT_OBJECT:
                            case MetadataPath::ARCHIVE_OBJECT:
                            case MetadataPath::HOLDING_OBJECT:
                                return true;
                            default:
                                $this->assertEquals(MetadataPath::FILE_OBJECT_PATH.$file->filename, $argument["object"]);
                                return true;
                        }
                        return false;
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
        $bag = $this->bag;

        $file = new \App\File([
            'bag_id' => $bag->id,
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

        $bagitUtil = Mockery::mock(BagitUtil::class);
        $bagitUtil->shouldReceive('addFile')->once();
        $bagitUtil->shouldReceive('createBag')->once()->andReturn(false);

        $metadataGenerator = Mockery::mock( MetadataGeneratorInterface::class, function( $mock ) {
            $mock->shouldReceive('createMetadataWriter')
                ->once()
                ->andReturn( Mockery::mock( MetadataWriterInterface::class, function( $mock ) {
                    $mock->shouldReceive('write')->times(MetadataPath::count())->andReturn(true);
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
