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
                 "path" => $file,
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
        $prefix = Str::uuid()."-";

        $this->app->instance( FileCollectorInterface::class, Mockery::mock(
            TarFileService::class, function ( $mock ) {
                $mock->shouldReceive( 'collectDirectory' )
                     ->once()
                     ->andReturn( true );
            }
        ));


        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) {
                $mock->shouldReceive( 'download' )
                    ->andReturn( Str::slug(3) );
            }
        ));
        $service = new FileArchiveService( $this->app,
            $this->app->make( ArchivalStorageInterface::class),
            $this->app->make( FileCollectorInterface::class)
        );
        $expected = Storage::disk( 'outgoing' )->path( "{$prefix}{$this->aip->external_uuid}" ).".tar";
        $actual = $service->buildTarFromAip( $this->aip, $prefix );
        $this->assertContains( $expected, $actual );
    }

    public function test_given_an_aip_has_three_files_when_building_a_tar_three_files_are_downloaded()
    {
        $prefix = Str::uuid()."-";
        $this->instance( FileCollectorInterface::class, Mockery::mock(
            TarFileService::class, function ( $mock ) {
                $mock->shouldReceive( 'collectDirectory' )
                     ->once();
            }
        ));

        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) {
                $mock->shouldReceive( 'download' )
                     ->times( 3 )
                     ->with( Mockery::type('\App\StorageLocation'), Mockery::type('string'), Mockery::type('string') )
                     ->andReturn( Str::slug(3) );
            }
        ));

        $service = new FileArchiveService( $this->app, $this->app->make( ArchivalStorageInterface::class),  $this->app->make( FileCollectorInterface::class)  );
        $service->buildTarFromAip( $this->aip, $prefix );
    }

    public function test_given_an_aip_when_adding_data_to_a_tar_the_resulting_tar_has_this_data_as_content()
    {
        $prefix = Str::uuid()."-";
        $texts = Collection::times(3, function() { return $this->faker->text(); } );
        $filesWithTexts = $this->files->combine( $texts );
        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) use ( $filesWithTexts, $prefix ) {
                $filesWithTexts->map( function( $text, $file ) use ( $mock, $prefix ) {

                    $relPath =  Str::after( json_decode($file)->fullpath, "{$this->aipAmPath}/" );  /* path relative to AIP root */
                    $filePath = "{$prefix}{$this->aip->external_uuid}/{$relPath}";
                    Storage::disk( 'outgoing' )->put( $filePath, $text );

                    $mock->shouldReceive( 'download' )
                         ->with( Mockery::type('\App\StorageLocation'), Mockery::type('string'), Mockery::type('string') )
                         ->andReturn( $filePath );
                });
            }
        ));

        $service = $this->app->make( FileArchiveInterface::class );
        $actualFilePath = $service->buildTarFromAip( $this->aip, $prefix );
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

    public function test_given_an_aip_when_incrementally_building_a_tar_from_it_the_resulting_tar_has_this_data_as_content()
    {
        $prefix = Str::uuid()."-";
        $texts = Collection::times(3, function() { return $this->faker->text(); } );
        $filesWithTexts = $this->files->pluck("path")->combine( $texts );

        $this->instance( FileCollectorInterface::class, Mockery::mock(
            TarFileService::class, function ( $mock ) use ( $filesWithTexts ) {
                $mock->shouldReceive( 'collectMultipleFiles' )
                     ->times(1)
                     ->with( Mockery::type('array'), Mockery::type('string'), Mockery::type('bool') )
                     ->andReturnUsing( function ( $files, $tarfile, $delete ) use ( $filesWithTexts ) {
                         $tar = new \PharData( $tarfile );
                         foreach ($files as $tarPath => $filePath) {
                             $dPath = Str::after( $filePath , "/" );
                             $tar->addFromString(  $tarPath, $filesWithTexts[$dPath] );
                         }
                         return true;
                     } );
            }
        ));

        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) use ( $filesWithTexts, $prefix ) {
                $filesWithTexts->map( function( $text, $file ) use ( $mock, $prefix ) {

                    $filePath = "{$prefix}{$this->aip->external_uuid}/{$file}";
                    Storage::disk( 'outgoing' )->put( $filePath, $text );

                    $mock->shouldReceive( 'download' )
                         ->with( Mockery::type('\App\StorageLocation'), Mockery::type('string'), Mockery::type('string') )
                         ->andReturnUsing(  function ($storageLocation, $storagePath, $destinationPath ){
                             return $destinationPath;
                         });
                });
            }
        ));
        $service = new FileArchiveService( $this->app,
            $this->app->make( ArchivalStorageInterface::class),
            $this->app->make( FileCollectorInterface::class)
        );

        $actualFilePath = $service->buildTarFromAipIncrementally( $this->aip, $prefix );
        $this->assertFileExists( $actualFilePath );

        $expected = $texts->reduce( function ( $carry, $item ) { return "{$carry}{$item}"; } );
        $tar = new \PharData( $actualFilePath );
        $actual = $filesWithTexts
            ->map( function( $text, $file ) use( $tar ) {
                $pharFileInfo = $tar->offsetGet( $file );
                return $pharFileInfo->getContent();
            })->reduce( function( $combined, $text ) {
                return "{$combined}{$text}";
            }, "");
        $this->assertNotEmpty( $actual );
        $this->assertEquals( $expected, $actual );
        $this->assertDirectoryNotExists( Storage::disk('outgoing')->path( $this->aip->external_uuid ) );    //Must clean up after tar'ing
    }

    public function test_given_multiple_aips_when_incrementally_building_a_tar_collection_from_it_the_aips_are_collected()
    {
        $prefix = Str::uuid()."-";
        $texts = Collection::times(3, function() { return $this->faker->text(); } );
        $filesWithTexts = $this->files->pluck("path")->combine( $texts );

        $this->instance( FileCollectorInterface::class, Mockery::mock(
            TarFileService::class, function ( $mock ) use ( $filesWithTexts ) {
                $mock->shouldReceive( 'collectMultipleFiles' )
                     ->times(3)
		     ->with( Mockery::type('array'), Mockery::type('string'), Mockery::type('bool') );
            } )
        );

        $this->instance( ArchivalStorageInterface::class, Mockery::mock(
            ArchivalStorageService::class, function ( $mock ) use ( $filesWithTexts, $prefix ) {
                $filesWithTexts->map( function( $text, $file ) use ( $mock, $prefix ) {

                    $filePath = "{$prefix}{$this->aip->external_uuid}/{$file}";
                    Storage::disk( 'outgoing' )->put( $filePath, $text );

                    $mock->shouldReceive( 'download' )
                         ->with( Mockery::type('\App\StorageLocation'), Mockery::type('string'), Mockery::type('string') )
                         ->andReturnUsing(  function ($storageLocation, $storagePath, $destinationPath ){
                             return $destinationPath;
                         });
                });
            }
        ));
        $service = new FileArchiveService( $this->app,
            $this->app->make( ArchivalStorageInterface::class),
            $this->app->make( FileCollectorInterface::class)
        );

        $actualFilePath = $service->buildTarFromAipCollectionIncrementally( [$this->aip, $this->aip] );
    }

}
