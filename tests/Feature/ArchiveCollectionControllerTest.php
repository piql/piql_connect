<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Faker\Factory as faker;
use App\Organization;
use App\Archive;
use App\Collection;
use App\User;
use App\Http\Resources\ArchiveResource;
use App\Http\Resources\CollectionResource;

class ArchiveCollectionControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $archive;
    private $organization;
    private $collection;
    private $collectionContent;
    private $faker;

    public function setUp() : void
    {
        parent::setUp();

        $this->organization = factory( Organization::class )->create();
        $this->archive = factory( Archive::class )->create(['organization_uuid' => $this->organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $this->organization->uuid]);
        Passport::actingAs( $this->user );
        $this->faker = Faker::create();

        $this->collectionContent = [ "archive_uuid" => $this->archive->uuid ];

        $this->collection = factory(Collection::class)->create( $this->collectionContent );

    }

    public function test_given_an_authenticated_user_when_getting_all_collections_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.collections.index', [ $this->archive->id ]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_requesting_a_collection_it_is_returned()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.collections.show', [$this->archive->id, $this->collection->id]) );
        $response->assertStatus( 200 )
            ->assertJsonFragment( $this->collectionContent );
    }

    public function test_given_an_authenticated_user_storing_a_collection_it_is_stored()
    {
        $collection = [
            'title' => $this->faker->slug(3),
            'description' => $this->faker->slug(6)
        ];

        $response = $this->actingAs( $this->user )
            ->postJson( route('admin.metadata.archives.collections.store', [$this->archive->id]),
                $collection );

        $response->assertStatus( 201 )
                 ->assertJsonStructure(["data" => ["id"]])
                 ->assertJsonFragment( $collection );
        $this->assertEquals(2, $this->archive->collections()->count());
    }

    public function test_given_an_authenticated_user_storing_a_collection_with_dublin_core_metadata_the_it_is_stored()
    {
        $metadata = [ "dc" => [
            'subject' => $this->faker->text()
        ]];

        $collection = [
            'title' => $this->faker->slug(3),
            'description' => $this->faker->slug(6),
            'defaultMetadataTemplate' => $metadata
        ];

        $response = $this->actingAs( $this->user )
            ->postJson( route('admin.metadata.archives.collections.store', [$this->archive->id]),
                $collection );

        $response->assertStatus( 201 )
                 ->assertJsonStructure(["data" => ["id"]])
                 ->assertJsonFragment( ["subject" => $metadata["dc"]["subject"]] );
    }


    public function test_given_an_authenticated_user_updating_when_updating_a_collection_it_is_updated()
    {
        $collection = [
            'title' => 'updated title',
            'description' => 'updated description',
        ];

        $response = $this->actingAs( $this->user )
                         ->patchJson(
                             route( 'admin.metadata.archives.collections.update',
                             [$this->archive->id, $this->collection->id] ),
                             $collection );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( $collection );
    }

    public function test_given_a_collection_when_updating_it_with_dublin_core_metadata_it_is_updated()
    {
        $metadataTemplate = [ "dc" => [
            'subject' => $this->faker->text()
        ]];

        $response = $this->actingAs( $this->user )
                         ->patchJson(
                             route( 'admin.metadata.archives.collections.update',
                             [$this->archive->id, $this->collection->id] ),
                             ["defaultMetadataTemplate" => $metadataTemplate ] );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["defaultMetadataTemplate" => $metadataTemplate ] );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }


    public function test_given_an_authenticated_user_when_deleting_a_collection_it_responds_405()
    {
        $response = $this->actingAs( $this->user )
            ->delete( route('admin.metadata.archives.collections.destroy', [$this->archive->id, $this->collection->id]));
        $response->assertStatus( 405 );
    }

    public function test_given_an_authenticated_user_when_getting_collection_metadata_it_is_in_the_response()
    {
        $collection = factory(Collection::class)->create(["defaultMetadataTemplate" => ["dc" => ["subject" => "testing things"]]]);
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.collections.show', [$this->archive->id, $collection->id]) );
        $response->assertStatus( 200 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] );
    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_with_updated_data()
    {
        $metadataTemplate = ["dc" => ["title" => "The best novel ever!"]];

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.archives.collections.update', [$this->archive->id, $this->collection->id]),
               ["defaultMetadataTemplate" => $metadataTemplate] );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'), ["defaultMetadataTemplate" => $metadataTemplate] );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( $expected );
    }

    public function test_given_an_authenticated_user_when_updating_a_collection_that_does_not_exist_it_is_upserted()
    {
        $data = [
            "title" => "This upsert concept just rocks",
            "defaultMetadataTemplate" => ["dc" => ["title" => "The best novel ever!"]],
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.archives.collections.upsert', [$this->archive] ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 201 )
            ->assertJsonFragment( $expected );
    }

    public function test_given_an_authenticated_user_when_upserting_a_collection_that_exists_it_is_updated()
    {
        $archive = factory(Archive::class)->create();
        $collection = factory(Collection::class)->create(["archive_uuid" => $archive->uuid]);

        $data = [
            "id" => $collection->id,
            "title" => "What a wonderful upsert",
            "defaultMetadataTemplate" => ["dc" => ["title" => "The most updated novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.archives.collections.upsert', [$this->archive] ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }

}
