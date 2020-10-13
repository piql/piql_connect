<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use App\StorageLocation;
use App\S3Configuration;
use Illuminate\Support\Facades\Log;

class StorageLocationApiTest extends TestCase
{
    use DatabaseTransactions;

    private $aipStorageLocation;
    private $dipStorageLocation;
    private $aipStorageLocationData;
    private $dipStorageLocationData;
    private $aipS3Configuration;
    private $dipS3Configuration;
    private $testUser;
    private $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->testUser = factory(\App\Auth\User::class)->create();
        Passport::actingAs($this->testUser);
        $this->faker = Faker::create();

        $this->aipS3Configuration = S3Configuration::create($this->makeS3ConfigurationData("aip"));
        $this->dipS3Configuration = S3Configuration::create($this->makeS3ConfigurationData("dip"));

        $this->aipStorageLocationData = $this->makeStorageLocationData(
            $this->testUser->id,
            $this->aipS3Configuration->id,
            "App\Aip"
        );
        $this->aipStorageLocation = StorageLocation::create($this->aipStorageLocationData);

        $this->dipStorageLocationData = $this->makeStorageLocationData(
            $this->testUser->id,
            $this->dipS3Configuration->id,
            'App\Dip'
        );
        $this->dipStorageLocation = StorageLocation::create($this->dipStorageLocationData);
    }

    private function makeS3ConfigurationData(string $bucketPrefix): array
    {
        return [
            'url' => $this->faker->url(),
            'key_id' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'secret' => $this->faker->regexify('[A-Za-z0-9]{40}'),
            'bucket' => $bucketPrefix . "-" . $this->faker->slug(2)
        ];
    }

    private function makeStorageLocationData(string $ownerId, int $locationId, string $storableType)
    {
        return [
            'owner_id' => $ownerId,
            'locatable_id' => $locationId,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => $storableType,
            'human_readable_name' => "Test storage location config"
        ];
    }

    public function test_when_requesting_a_list_of_storage_locations_it_returns_200()
    {
        $response = $this->get(route('storage.config.locations'));
        $response->assertStatus(200);
    }

    public function test_when_requesting_a_list_of_storage_locations_it_returns_well_formed_json()
    {
        $response = $this->get(route('storage.config.locations'));
        $response->assertJson(['data' => []]);
    }

    public function test_when_requesting_a_list_of_storage_locations_it_returns_only_locations_for_the_authenticated_user()
    {
        $otherUser = factory(\App\Auth\User::class)->create();
        $otherStorageLocationData = ['owner_id' => $otherUser->id] + $this->aipStorageLocationData;
        StorageLocation::create($otherStorageLocationData);

        $response = $this->get(route('storage.config.locations'));
        $response->assertJsonMissing(['owner_id' => $otherUser->id]);
    }

    public function test_given_a_storage_location_exists_when_requesting_by_it_id_it_returns_the_resource()
    {
        $locationId = $this->aipStorageLocation->id;
        $response = $this->get(route('storage.config.location', ['id' => $locationId]));
        $response->assertJson(['data' => $this->aipStorageLocationData]);
    }

    public function test_given_valid_parameters_when_creating_a_storage_location_it_returns_the_created_resource()
    {
        $storageLocation = $this->makeStorageLocationData($this->testUser->id, $this->aipS3Configuration->id, "App\Aip");
        $response = $this->post(route('storage.config.location.new'), $storageLocation);
        $response->assertJson(['data' => $storageLocation]);
    }

    public function test_given_an_invalid_location_id_when_creating_a_storage_location_it_returns_the_error()
    {
        $storageLocation = $this->makeStorageLocationData($this->testUser->id, S3Configuration::count() + 1000, "App\Aip");
        $response = $this->post(route('storage.config.location.new'), $storageLocation);
        $response->assertStatus(422)
            ->assertJson(['errors' =>  [
                'status' => 422,
                'details' => [
                    'locatable_id' => ['The selected locatable id is invalid.']
                ]
            ]]);
    }
}
