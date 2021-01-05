<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;
use Faker\Factory as Faker;
use App\Collection;

class CollectionApiTest extends TestCase
{
    use DatabaseTransactions;

    private $validCollectionData;
    private $validCollectionDataCreated;
    private $createdTestCollection;

    public function setUp() : void
    {
        parent::setUp();
        $testUser = factory( \App\User::class )->create();
        Passport::actingAs( $testUser );

        $faker = Faker::create();
        $this->validCollectionDataCreated = [
            'uuid' => $faker->uuid(),
            'title' => $faker->company(),
            'description' => $faker->text(100)
        ];

        $this->validCollectionData = [
            'uuid' => $faker->uuid(),
            'title' => $faker->company(),
            'description' => $faker->text(100)
        ];

        $this->createdTestCollection = Collection::create($this->validCollectionDataCreated);
    }

    public function test_it_can_get_a_list_of_collections()
    {
        $response = $this->get( route( 'api.metadata.collections.index' ) );
        $response->assertStatus( 200 );
    }

    public function test_when_requesting_a_list_of_collections_it_returns_a_valid_json_array()
    {
        $response = $this->get(route('api.metadata.collections.index'));
        $response->assertJsonStructure(['data' => []]);
    }

    public function test_when_creating_a_collection_it_responds_with_403()
    {
        $response = $this->json('POST',
            route('api.metadata.collections.store'),
            $this->validCollectionData);
        $response->assertStatus(403);
    }

    public function test_given_a_collection_exists_when_deleting_it_it_is_forbidden()
    {
        $collectionId = $this->createdTestCollection->id;
        $response = $this->json( 'DELETE',
            route( 'api.metadata.collections.destroy', ['id' => $collectionId] )
        );
        $response->assertStatus(403);
    }

    public function test_given_a_collection_does_not_exist_when_deleting_it_responds_with_403()
    {
        $collectionId = PHP_INT_MAX;
        $response = $this->json( 'DELETE',
            route( 'api.metadata.collections.destroy', ['id' => $collectionId] )
        );

        $response->assertStatus(403);
    }

    public function test_given_a_collection_exists_when_requesting_it_it_is_returned()
    {
        $collectionId = $this->createdTestCollection->id;
        $response = $this->json( 'GET',
            route( 'api.metadata.collections.show', ['id' => $collectionId] )
        );

        $response->assertStatus(200);
        $response->assertJson([ 'data' => $this->validCollectionDataCreated ]);
    }

    public function test_given_a_collection_does_not_exist_when_requesting_it_responds_with_a_404_and_error_message()
    {
        $collectionId = PHP_INT_MAX;
        $response = $this->json( 'GET',
            route( 'api.metadata.collections.show', ['id' => $collectionId] )
        );

        $response->assertStatus(404);
        $response->assertJson(['error' => 404, 'message' => 'No Collection with id '.$collectionId.' was found.']);
    }
}
