<?php

namespace Tests\Feature;

use App\Interfaces\ArchivalStorageInterface;
use App\Interfaces\FilesystemDriverInterface;
use App\Jobs\TransferPackageToStorage;
use App\S3Configuration;
use App\Services\ArchivalStorageService;
use App\Services\ArchivematicaConnectionService;
use App\StorageLocation;
use App\Aip;
use App\Dip;
use App\Bag;
use App\FileObject;
use App\MetadataPath;
use Faker\Factory as faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Webpatser\Uuid\Uuid;

class TransferPackageToStorageTest extends TestCase
{
    use DatabaseTransactions;

    private $storageLocation;
    private $s3Configuration;
    private $storageLocationData;
    private $storageService;
    private $testUser;
    private $faker;
    private $aip;
    private $dip;
    private $bag;

    private $destinationStorage;
    private $sourceStorage;

    public function setUp() : void
    {
        parent::setUp();

        $organization = factory(\App\Organization::class)->create();
        factory(\App\Archive::class)->create(['organization_uuid' => $organization->uuid]);
        $this->testUser = factory(\App\User::class)->create(['organization_uuid' => $organization->uuid]);
        Passport::actingAs( $this->testUser );
        $this->faker = Faker::create();

        $this->destinationStorage = Storage::fake("dst");
        $this->sourceStorage = Storage::fake("src");

        $this->s3Configuration = S3Configuration::create( $this->makeS3ConfigurationFromEnv( "aip" ) );

        $this->storageLocationData = $this->makeStorageLocationData(
            $this->testUser->id, $this->s3Configuration->id, "App\Aip" );
        $this->storageLocation = StorageLocation::create( $this->storageLocationData );

        $this->bag = Bag::create(['owner' => $this->testUser->id, 'name' => "testEventBag"]);

        $this->dip = Dip::create([
            'bag_uuid' => $this->bag->uuid,
            'external_uuid' => Uuid::generate(),
            'owner' => $this->testUser->id
        ]);

        $this->aip = Aip::create([
            'bag_uuid' => $this->bag->uuid,
            'external_uuid' => Uuid::generate(),
            'owner' => $this->testUser->id
        ]);

        $this->bag->storage_properties->update([
            'aip_uuid' => $this->aip->external_uuid,
            'dip_uuid' => $this->dip->external_uuid
        ]);


    }

    private function makeS3ConfigurationFromEnv( string $bucketPrefix ) : array
    {
        return [
            'url' => env( 'AWS_URL' ),
            'key_id' => env( 'AWS_ACCESS_KEY_ID' ),
            'secret' => env( 'AWS_SECRET_ACCESS_KEY' ),
            'bucket' => env( 'AWS_BUCKET' )
        ];
    }


    private function makeStorageLocationData( string $ownerId, int $locationId, string $storableType )
    {
        return [
            'owner_id' => $ownerId,
            'locatable_id' => $locationId,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => $storableType,
            'human_readable_name' => "Storage config name"
        ];
    }

    public function test_upload_DIP_with_4_files()
    {
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')
                         ->times(4)
                         ->andReturns($this->destinationStorage);

        $this->app->bind('App\Interfaces\FilesystemDriverInterface',
            function( $app ) use($filesystemDriver) {
                return $filesystemDriver;
            }
        );

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');
        $basePath = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/';
        $this->sourceStorage->makeDirectory( $basePath );

        $testFiles = Collection::times( 4 , function ( $index ) use ( $basePath ) {
            return $this->faker->file( "/tmp", $this->sourceStorage->path($basePath), false );
        });

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath, 'App\Dip', $this->dip->id );
        $job->handle( $storageService);
        $actual = $testFiles->every( function ( $file ) use ( $basePath ) {
            return $this->destinationStorage->exists( $basePath.$file );
        });
        $this->assertTrue( $actual );
    }

    public function test_upload_AIP_with_1_files()
    {
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')
                         ->once()
                         ->andReturns($this->destinationStorage);

        $this->app->bind('App\Interfaces\FilesystemDriverInterface',
            function( $app ) use($filesystemDriver) {
                return $filesystemDriver;
            }
        );

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');
        $basePath = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/';
        $this->sourceStorage->makeDirectory( $basePath );

        $testFile = $this->faker->file( "/tmp", $this->sourceStorage->path($basePath), false );

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath, 'App\Aip', $this->aip->id );
        $job->handle( $storageService );
        $this->assertFileExists( $this->destinationStorage->path( $basePath.$testFile ) );
    }

    public function test_upload_AIP_with_METS_file()
    {
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')
                         ->twice()
                         ->andReturns($this->destinationStorage);

        $this->app->bind('App\Interfaces\FilesystemDriverInterface',
            function( $app ) use($filesystemDriver) {
                return $filesystemDriver;
            }
        );

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');
        $basePath = $this->aip->external_uuid."/";
        $this->sourceStorage->makeDirectory( $basePath );
        $this->sourceStorage->makeDirectory( "{$basePath}data/objects/" . MetadataPath::FILE_OBJECT_PATH );
        $mets = file_get_contents( "tests/Data/piqlconnect-v1-METS.90d56eb5-9184-4081-a925-2d6fe6581ecd.xml" );
        $metsPath = "{$basePath}data/METS.{$this->aip->external_uuid}.xml";
        $this->sourceStorage->put( $metsPath, $mets );

        $testFile = $this->faker->file( "/tmp", $this->sourceStorage->path($basePath), false );
        $fileFromMets = "{$basePath}data/objects/" . MetadataPath::FILE_OBJECT_PATH . "build.pri";
        $this->sourceStorage->move( "{$basePath}{$testFile}", $fileFromMets );
        $this->assertFileExists( $this->sourceStorage->path( $fileFromMets ) );

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath, 'App\Aip', $this->aip->id );
        $job->handle( $storageService );
        $this->assertFileExists( $this->destinationStorage->path( $fileFromMets ) );
        $this->assertFileExists( $this->destinationStorage->path( $metsPath ) );

        $expected =
            ["mets" =>
                [ "dc" => [
                    "title" => "Build priority",
                    "creator" => "Piql AS",
                    "subject" => "Build priority file",
                    "description" => "Part of the cmu_112 project",
                    "publisher" => "Piql AS",
                    "contributor" => "Piql AS",
                    "date" => "2020-09-16",
                    "type" => "C++ project file",
                    "format" => "pri",
                    "identifier" => "",
                    "source" => "",
                    "language" => "",
                    "relation" => "",
                    "coverage" => "",
                    "rights" => ""
                ]
            ]
        ];

        $actual = FileObject::whereHasMorph("storable", ["App\Aip"],
            function ( $q) {
                $q->where('external_uuid', $this->aip->external_uuid);
            } )->where("filename","build.pri")
               ->first()
               ->metadata->first()->metadata;
        $this->assertEquals( $expected, $actual );
    }

    public function test_upload_AIP_with_non_existing_path()
    {
        $fileCount = 0;
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')->times($fileCount)->andReturns($this->destinationStorage);
        $this->app->bind('App\Interfaces\FilesystemDriverInterface', function( $app ) use($filesystemDriver) {
            return $filesystemDriver;
        });

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^Upload failed: file does not exist/'));

        $basePath = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/';
        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath, 'App\Aip', $this->aip->id );
        $job->handle( $storageService);
        self::assertEquals(0, count($this->destinationStorage->allFiles($basePath)), "");

    }


}
