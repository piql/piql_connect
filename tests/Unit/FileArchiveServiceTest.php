<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Mockery;
use App\Interfaces\FileArchiveInterface;
use App\Services\FileArchiveService;
use App\Interfaces\ArchivalStorageInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Faker\Factory as faker;

class FileArchiveServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $service;
    private $aip;
    private $files;
    private $storageLocation;
    private $aipFiles;
    private $faker;
    private $destinationPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
        $this->aipFiles = collect([
            "data/METS.8149cfad-2ba1-4ccf-b132-255dd6399ed1.xml",
            "manifest-sha256",
            "data/thumbnails/9cc3eac1-8e23-462c-984d-2d3ed078f803.jpg",
        ]);
        $this->storageLocation = factory( \App\StorageLocation::class )->states( 'fileArchiveServiceTest' )->create();
        $sipUuid = Str::uuid()->toString();
        $aipAmPath = implode( "/", str_split( str_replace( "-","", $sipUuid ) ,4 ) ) . "/" . $this->faker->slug( 10 );
        $this->aip = factory( \App\Aip::class )->states( 'fileArchiveServiceTest' )->create([
            'online_storage_path' => $aipAmPath,
            'online_storage_location_id' => $this->storageLocation->id
        ]);
        $this->files = $this->aipFiles->map( function( $file ) use ( $aipAmPath ) {
            return factory( \App\FileObject::class )->states( 'fileArchiveServiceTest' )
             ->create([
                 "filename" => basename( $file ),
                 "fullpath" => "${aipAmPath}/${file}",
                 "storable_type" => "App\Aip",
                 "storable_id" => $this->aip->id
             ]);
        });
        $this->destinationPath = Storage::fake( 'outgoing' )->path( "{$this->aip->external_uuid}" );
    }

    public function tearDown(): void
    {
        Storage::fake( 'outgoing' )->deleteDirectory( $this->aip->external_uuid );
        parent::tearDown();
    }

    public function test_when_injecting_an_instance_of_filearchiveserviceinterface_it_makes_a_filearchiveservice()
    {
        $service = $this->app->make( FileArchiveInterface::class );
        $this->assertInstanceOf( FileArchiveService::class, $service );
    }

    public function test_when_getting_aip_filenames_they_are_returned()
    {
        $service = $this->app->make( FileArchiveInterface::class );
        $expected = $this->aipFiles->toArray();
        $actual = $service->getAipFileNames ( $this->aip );
        $this->assertEquals( $expected, $actual );
    }

    public function test_when_building_tars_it_returns_the_path_of_the_tar()
    {
        $service = $this->app->make( FileArchiveInterface::class );
        $expected = Storage::disk( 'outgoing' )->path( $this->aip->external_uuid );
        $actual = $service->buildTarFromAip( $this->aip );
        $this->assertEquals( $expected, $actual );
    }

    public function test_when_downloading_a_file_the_file_is_downloaded_to_the_expected_location_with_the_right_contents()
    {
        $filePath = "{$this->aip->external_uuid}/{$this->aipFiles[0]}";
        $contents = $this->faker->text();
        $this->instance( ArchivalStorageInterface::class, Mockery::mock( 
            ArchivalStorageService::class, function ( $mock ) use ( $filePath, $contents ) {
                $mock->shouldReceive( 'download' )->once()
                     ->withArgs( function ( \App\StorageLocation $storageLocation, string $storagePath, string $destinationPath )
                         use ( $filePath ) {
                             return basename( $storagePath ) == basename( $filePath );
                         })
                         ->andReturnUsing( function( $result ) use( $filePath, $contents ) {
                             Storage::disk( 'outgoing' )->put( $filePath, $contents );
                             return $filePath;
                         });

            }
        ));

        $service = $this->app->make( FileArchiveInterface::class );
        $actual = $service->downloadFile( $this->aip, $this->files[0] ); 
        $this->assertFileExists( Storage::disk('outgoing')->path( $actual ) );
        $this->assertStringEqualsFile( Storage::disk('outgoing')->path($filePath ), $contents );
    }

    public function test_when_downloading_an_aip_the_files_are_downloaded_to_the_expected_location()
    {
        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) {
                $this->aip->fileObjects->map( function( $fileObject, $index ) use ( $mock ) {
                    $mock->shouldReceive( 'download' )
                         ->withArgs( function ( \App\StorageLocation $storageLocation, string $storagePath, string $destinationPath )
                             use ($index) {
                                 return basename( $storagePath ) == basename( $this->aipFiles[$index] );
                             })
                             ->andReturnUsing( function( $result ) use( $index ) {
                                 $filePath = "{$this->aip->external_uuid}/{$this->aipFiles[$index]}";
                                 Storage::disk( 'outgoing' )->put( $filePath, $this->faker->text() );
                                 return $filePath;
                             });
                });
            }
        ));

        $service = $this->app->make( FileArchiveInterface::class );
        $actual = $service->downloadAipFiles( $this->aip );
        $this->files = $this->aipFiles->map( function( $file ) {
            $resultingFullPath = $this->aip->external_uuid . "/" . $file;
            $this->assertFileExists( Storage::disk( 'outgoing' )->path( $resultingFullPath ) );
        });
    }

}
