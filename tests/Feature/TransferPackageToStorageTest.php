<?php

namespace Tests\Feature;

use App\Interfaces\ArchivalStorageInterface;
use App\Interfaces\FilesystemDriverInterface;
use App\Jobs\TransferPackageToStorage;
use App\S3Configuration;
use App\Services\ArchivalStorageService;
use App\Services\ArchivematicaConnectionService;
use App\StorageLocation;
use Faker\Factory as faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;

class TransferPackageToStorageTest extends TestCase
{
    use DatabaseTransactions;

    private $storageLocation;
    private $s3Configuration;
    private $storageLocationData;
    private $storageService;
    private $testUser;
    private $faker;

    private $destinationStorage;
    private $sourceStorage;

    public function setUp() : void
    {
        parent::setUp();

        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );
        $this->faker = Faker::create();

        $this->destinationStorage = Storage::fake();
        $this->sourceStorage = Storage::fake();

        $this->s3Configuration = S3Configuration::create( $this->makeS3ConfigurationFromEnv( "aip" ) );

        $this->storageLocationData = $this->makeStorageLocationData(
            $this->testUser->id, $this->s3Configuration->id, "App\Aip" );
        $this->storageLocation = StorageLocation::create( $this->storageLocationData );

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
        $fileCount = 4;
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')->times($fileCount)->andReturns($this->destinationStorage);
        $this->app->bind('App\Interfaces\FilesystemDriverInterface', function( $app ) use($filesystemDriver) {
            return $filesystemDriver;
        });

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');

        $basePath = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/';
        $files = array();
        for($n = 0; $n < $fileCount; $n++) {
            $path = $basePath."file".$n.".txt";
            $this->sourceStorage->put($path, $path);
            $files[] = $path;
        }

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath);
        $job->handle( $storageService);
        foreach ($files as $file) {
            $this->assertFileExists($this->destinationStorage->path($file));
        }
    }

    public function test_upload_DIP_with_0_files()
    {
        $fileCount = 0;
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')->times($fileCount)->andReturns($this->destinationStorage);
        $this->app->bind('App\Interfaces\FilesystemDriverInterface', function( $app ) use($filesystemDriver) {
            return $filesystemDriver;
        });

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');
        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Uploading files to /'));
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^Upload don\'t support directory uploads/'));

        $basePath = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/';
        $this->sourceStorage->makeDirectory($basePath);

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath);
        $job->handle( $storageService);
        self::assertEquals(0, count($this->destinationStorage->allFiles($basePath)), "");
    }

    public function test_upload_AIP_with_1_files()
    {
        $fileCount = 1;
        $filesystemDriver = Mockery::mock(FilesystemDriverInterface::class);
        $filesystemDriver->expects('createDriver')->times($fileCount)->andReturns($this->destinationStorage);
        $this->app->bind('App\Interfaces\FilesystemDriverInterface', function( $app ) use($filesystemDriver) {
            return $filesystemDriver;
        });

        $storageService = $this->app->make('App\Interfaces\ArchivalStorageInterface');

        $path = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/file.txt';
        $files = array();
        $this->sourceStorage->put($path, $path);

        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $path);
        $job->handle( $storageService);
        foreach ($files as $file) {
            $this->assertFileExists($this->destinationStorage->path($file));
        }
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
        Log::shouldReceive('info')->times(1)->with(\Mockery::pattern('/^Uploading files to /'));
        Log::shouldReceive('error')->times(1)->with(\Mockery::pattern('/^Upload failed: file does not exist/'));

        $basePath = '0753/029c/165e/4ec4/9117/2844/9ff6/8cf9/';
        $job = new TransferPackageToStorage($this->storageLocation, $this->sourceStorage, $basePath);
        $job->handle( $storageService);
        self::assertEquals(0, count($this->destinationStorage->allFiles($basePath)), "");

    }


}
