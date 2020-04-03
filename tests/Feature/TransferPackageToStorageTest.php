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
use App\FileObject;
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

        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );
        $this->faker = Faker::create();

        $this->destinationStorage = Storage::fake("dst");
        $this->sourceStorage = Storage::fake("src");

        $this->s3Configuration = S3Configuration::create( $this->makeS3ConfigurationFromEnv( "aip" ) );

        $this->storageLocationData = $this->makeStorageLocationData(
            $this->testUser->id, $this->s3Configuration->id, "App\Aip" );
        $this->storageLocation = StorageLocation::create( $this->storageLocationData );

        $this->dip = Dip::create([
            'bag_uuid' => Uuid::generate(),
            'external_uuid' => Uuid::generate(),
            'owner' => $this->testUser->id
        ]);

        $this->aip = Aip::create([
            'bag_uuid' => Uuid::generate(),
            'external_uuid' => Uuid::generate(),
            'owner' => $this->testUser->id
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
        $this->sourceStorage->makeDirectory( "{$basePath}data/objects" );
        $mets = file_get_contents( "tests/Data/gotmetadata-METS.8149cfad-2ba1-4ccf-b132-255dd6399ed1.xml" );
        $metsPath = "{$basePath}data/METS.{$this->aip->external_uuid}.xml";
        $this->sourceStorage->put( $metsPath, $mets );

        $testFile = $this->faker->file( "/tmp", $this->sourceStorage->path($basePath), false );
        $fileFromMets = "{$basePath}data/objects/FMU.420001.tif";
        $this->sourceStorage->move( "{$basePath}{$testFile}", $fileFromMets );
        $this->assertFileExists( $this->sourceStorage->path( $fileFromMets ) );

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath, 'App\Aip', $this->aip->id );
        $job->handle( $storageService );
        $this->assertFileExists( $this->destinationStorage->path( $fileFromMets ) );
        $this->assertFileExists( $this->destinationStorage->path( $metsPath ) );

        $expected =
            ["mets" =>
                [ "dc" => [
                    "title" => "Krigsskip",
                    "creator" => "Forsvarsmuseet",
                    "subject" => "Skip",
                    "description" => "Et skip som kjører fort",
                    "publisher" => "En eller annen veteran",
                    "contributor" => "Noen folk",
                    "date" => "03.23.2020",
                    "type" => "Image",
                    "format" => "TIF",
                    "identifier" => "FMU.420001",
                    "source" => "Scanning",
                    "language" => "Norsk",
                    "relation" => "Ingen",
                    "coverage" => "Bra",
                    "rights" => "Public Domain"
                ]
            ]
        ];

        $actual = FileObject::whereHasMorph("storable", ["App\Aip"],
            function ( $q) {
                $q->where('external_uuid', $this->aip->external_uuid);
            } )->where("filename","FMU.420001.tif")
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
