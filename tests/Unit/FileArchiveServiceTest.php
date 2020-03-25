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
    private $aipAmPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
        $this->aipFiles = collect([
            "data/METS.8149cfad-2ba1-4ccf-b132-255dd6399ed1.xml",
            "data/thumbnails/9cc3eac1-8e23-462c-984d-2d3ed078f803.jpg",
            "manifest-sha256",
        ]);
        $this->storageLocation = factory( \App\StorageLocation::class )->states( 'fileArchiveServiceTest' )->create();
        $sipUuid = Str::uuid()->toString();
        $this->aipAmPath = implode( "/", str_split( str_replace( "-","", $sipUuid ) ,4 ) ) . "/" . $this->faker->slug( 10 );
        $this->aip = factory( \App\Aip::class )->states( 'fileArchiveServiceTest' )->create([
            'online_storage_path' => $this->aipAmPath,
            'online_storage_location_id' => $this->storageLocation->id
        ]);
        $this->files = $this->aipFiles->map( function( $file ) {
            return factory( \App\FileObject::class )->states( 'fileArchiveServiceTest' )
             ->create([
                 "filename" => basename( $file ),
                 "fullpath" => "{$this->aipAmPath}/{$file}",
                 "storable_type" => "App\Aip",
                 "storable_id" => $this->aip->id
             ]);
        });
        $this->destinationPath = Storage::fake( 'outgoing' )->path( "{$this->aip->external_uuid}" );
    }

    public function tearDown(): void
    {
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

    public function test_when_downloading_an_aip_the_right_files_are_downloaded_to_the_expected_location()
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

    public function test_when_building_tars_it_returns_the_full_path_of_the_tar()
    {
        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) {
                $mock->shouldReceive( 'download' )
                    ->andReturn( Str::slug(3) );
            }
        ));
        $service = $this->app->make( FileArchiveInterface::class );
        $exp = Storage::disk( 'outgoing' )->path( $this->aip->external_uuid );
        $expected = "{$exp}.tar";
        $actual = $service->buildTarFromAip( $this->aip );
        $this->assertEquals( $expected, $actual );
    }

    public function test_given_an_aip_has_three_files_when_building_a_tar_three_files_are_downloaded()
    {
        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) {
                $mock->shouldReceive( 'download' )
                     ->times( 3 )
                     ->with( Mockery::type('\App\StorageLocation'), Mockery::type('string'), Mockery::type('string') )
                     ->andReturn( Str::slug(3) );
            }
        ));
        $service = $this->app->make( FileArchiveInterface::class );
        $service->buildTarFromAip( $this->aip );
    }

    public function test_given_an_aip_when_adding_data_to_a_tar_the_resulting_tar_has_this_data_as_content()
    {
        $texts = Collection::times(3, function() { return $this->faker->text(); } );
        $filesWithTexts = $this->files->combine( $texts );
        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) use ( $filesWithTexts ) {
                $filesWithTexts->map( function( $text, $file ) use ( $mock ) {

                    $relPath =  Str::after( json_decode($file)->fullpath, "{$this->aipAmPath}/" );  /* path relative to AIP root */
                    $filePath = "{$this->aip->external_uuid}/{$relPath}";
                    Storage::disk( 'outgoing' )->put( $filePath, $text );

                    $mock->shouldReceive( 'download' )
                         ->with( Mockery::type('\App\StorageLocation'), Mockery::type('string'), Mockery::type('string') )
                         ->andReturn( $filePath );
                });
            }
        ));

        $service = $this->app->make( FileArchiveInterface::class );
        $actualFilePath = $service->buildTarFromAip( $this->aip );
        $this->assertFileExists( $actualFilePath );

        $expected = $texts->reduce( function ( $carry, $item ) { return "{$carry}{$item}"; } );
        $tar = new \PharData( $actualFilePath );
        $actual = $filesWithTexts
            ->map( function( $text, $file ) use( $tar ) {
                $relPath =  Str::after( json_decode($file)->fullpath, "{$this->aipAmPath}/" );
                $pharFileInfo = $tar->offsetGet( $relPath );
                return $pharFileInfo->getContent();
            })->reduce( function( $combined, $text ) {
                return "{$combined}{$text}";
            }, "");
        $this->assertNotEmpty( $actual );
        $this->assertEquals( $expected, $actual );
        $this->assertDirectoryNotExists( Storage::disk('outgoing')->path( $this->aip->external_uuid ) );    //Must clean up after tar'ing
    }

}
