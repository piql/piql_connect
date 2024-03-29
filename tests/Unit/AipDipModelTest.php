<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Faker\Factory as faker;
use Webpatser\Uuid\Uuid;
use App\Aip;
use App\Bag;
use App\Dip;
use App\StorageLocation;
use App\StorageProperties;
use App\S3Configuration;

class AipDipModelTest extends TestCase
{
    use DatabaseTransactions;

    private $external_aip_uuid;
    private $external_dip_uuid;
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();

        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $this->external_aip_uuid = Uuid::generate();
        $this->external_dip_uuid = Uuid::generate();
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


    public function test_given_an_external_id_when_creating_an_aip_it_is_created()
    {

        $aip = Aip::create([ 
            'external_uuid' => $this->external_aip_uuid,
            'owner' => $this->testUser->id
        ]);

        $this->assertNotNull( $aip );
    }

    public function test_given_an_external_id_when_creating_a_dip_it_is_created()
    {
        $dip = Dip::create([ 
            'external_uuid' => $this->external_dip_uuid,
            'owner' => $this->testUser->id
        ]);
        $this->assertNotNull( $dip );
    }

    public function test_given_an_aip_and_a_dip_when_setting_the_related_aip_on_the_dip_then_they_are_related()
    {

       $aip = Aip::create([ 
            'external_uuid' => $this->external_aip_uuid,
            'owner' => $this->testUser->id
        ]);

       $dip = Dip::create([ 
            'external_uuid' => $this->external_dip_uuid,
            'owner' => $this->testUser->id,
            'aip_external_uuid' => $aip->external_uuid
        ]);

        $this->assertEquals( $this->external_aip_uuid, $dip->aip->external_uuid );
    }

    public function test_given_a_storage_properties_when_it_has_an_aip_and_a_dip_the_aip_can_get_the_dip()
    {
        $aip = Aip::create([ 
            'external_uuid' => $this->external_aip_uuid,
            'owner' => $this->testUser->id
        ]);

        $dip = Dip::create([ 
            'external_uuid' => $this->external_dip_uuid,
            'owner' => $this->testUser->id,
            'aip_external_uuid' => $aip->external_uuid
        ]);

        $bag = Bag::create(['owner' => $this->testUser->id, 'name' => "testAipDipRelationBag"]);
        $bag->storage_properties->update([
            'aip_uuid' => $aip->external_uuid,
            'dip_uuid' => $dip->external_uuid
        ]);

        $this->assertEquals( $this->external_dip_uuid, $aip->storagePropertiesDip()->external_uuid );
    }


    public function test_given_a_storage_properties_when_it_has_an_aip_and_a_dip_the_dip_can_get_the_aip()
    {
        $aip = Aip::create([ 
            'external_uuid' => $this->external_aip_uuid,
            'owner' => $this->testUser->id
        ]);

        $dip = Dip::create([ 
            'external_uuid' => $this->external_dip_uuid,
            'owner' => $this->testUser->id,
            'aip_external_uuid' => $aip->external_uuid
        ]);

        $bag = Bag::create(['owner' => $this->testUser->id, 'name' => "testAipDipRelationBag"]);
        $bag->storage_properties->update([
            'aip_uuid' => $aip->external_uuid,
            'dip_uuid' => $dip->external_uuid
        ]);

        $this->assertEquals( $this->external_aip_uuid, $dip->storagePropertiesAip()->external_uuid );
    }


    public function test_given_a_storage_location_id_and_path_when_creating_an_aip_it_is_has_online_storage()
    {

        $s3Config = S3Configuration::create( $this->makeS3ConfigurationFromEnv( "aip" ) );
        $storageLocationData = $this->makeStorageLocationData(
            $this->testUser->id, $s3Config->id, "App\Aip" );
        $storageLocation = StorageLocation::create( $storageLocationData );

        $aip = Aip::create([ 
            'external_uuid' => $this->external_aip_uuid,
            'owner' => $this->testUser->id,
            'online_storage_location_id' => $storageLocation->id,
            'online_storage_path' => '/some/File.tar'
        ]);

        $this->assertEquals( $aip->online_storage_location_id, $storageLocation->id );
        $this->assertEquals( $aip->online_storage_path, '/some/File.tar' );

    }


}
