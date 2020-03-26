<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use App\StorageLocation;
use App\S3Configuration;


class TransferApiTest extends TestCase
{
    use DatabaseTransactions;

    private $storageLocation;
    private $storageLocationData;
    private $s3Configuration;
    private $testUser;
    private $faker;
    private $storageService;
    private $targetPath;
    private $storage;

    public function setUp( ) : void
    {
        parent::setUp();
        $filePath = "/target.txt";
        $this->storage = Storage::fake('outgoing');
        $this->storage->put($filePath, "Hello world");
        $this->targetPath = $this->storage->path('');

        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );
        $this->faker = Faker::create();
        $this->s3Configuration = S3Configuration::create( $this->makeS3ConfigurationFromEnv( "aip" ) );

        $this->storageLocationData = $this->makeStorageLocationData(
            $this->testUser->id, $this->s3Configuration->id, "App\Aip" );
        $this->storageLocation = StorageLocation::create( $this->storageLocationData );

        $this->storageService = resolve( 'App\Interfaces\ArchivalStorageInterface' );
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

    public function test_when_requesting_a_directory_listing_it_responds_with_200_ok()
    {
        $response = $this->get( route( 'storage.transfer.ls', $this->storageLocation->id) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_a_directory_listing_it_responds_with_well_formed_json()
    {
        $response = $this->get( route( 'storage.transfer.ls', $this->storageLocation->id) );
        $response->assertjson([ 'data' => ['files' => [] ] ]);
    }

    public function test_when_uploading_a_local_file_to_remote_storage_it_responds_with_the_remote_file_name()
    {
        $testFilePath = $this->faker->file( $this->targetPath );
        $payload = [ "data" => ["path" => $testFilePath ] ];
        $response = $this->post( route( 'storage.transfer.upload', $this->storageLocation->id ), $payload );

        $response->assertStatus(200)
                 ->assertJson([ 'data' => pathinfo( $testFilePath, PATHINFO_BASENAME ) ]);

        $testFileBasename = pathinfo( $testFilePath, PATHINFO_BASENAME );
        $this->storageService->delete( $this->storageLocation, $testFileBasename );
    }

    public function test_when_downloading_a_remote_file_to_local_storage_it_responds_with_the_local_file_path()
    {
        $testFilePath = $this->faker->file( $this->targetPath );
        $this->storageService->upload( $this->storageLocation, "", $testFilePath );

        $downloadLocalPath = "/testDownloads";
        $testFileBasename = pathinfo( $testFilePath, PATHINFO_BASENAME );
        $payload = [ "data" => ["remotePath" => $testFileBasename, "localPath" => $downloadLocalPath ] ];
        $response = $this->post( route( 'storage.transfer.download',
            $this->storageLocation->id ), $payload );

        $this->storageService->delete( $this->storageLocation, $testFileBasename );

        $expected = $this->storage->path( $downloadLocalPath );
        $response->assertStatus(200)
                 ->assertJson([ 'data' => $expected ]);

    }

    public function test_when_deleting_a_remote_file_it_is_deleted()
    {
        $testFilePath = $this->faker->file( $this->targetPath );
        $this->storageService->upload( $this->storageLocation, "", $testFilePath );

        $testFileBasename = pathinfo( $testFilePath, PATHINFO_BASENAME );
        $payload = [ "data" => ["path" => $testFileBasename ] ];
        $response = $this->post( route( 'storage.transfer.deletion',
            $this->storageLocation->id ), $payload );

        $response->assertStatus(200)
                 ->assertJson(['data' => [ 'message' => "Successfully deleted file ".$testFileBasename ] ]);

        $remoteState = $this->storageService->ls( $this->storageLocation );
        $this->assertNotContains( $testFileBasename, $remoteState );
    }
}
