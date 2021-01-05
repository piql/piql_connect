<?php

namespace Tests\Feature;

use App\Collection;
use App\Holding;
use App\Http\Resources\ArchiveResource;
use App\Organization;
use App\Archive;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\HoldingResource;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class ArchiveCollectionHoldingControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $archive;
    private $collection;
    private $holding;

    public function setUp() : void
    {
        parent::setUp();

        $org = factory(Organization::class)->create();
        $this->archive = factory(Archive::class)->create([
            'organization_uuid' => $org->uuid
        ]);
        $this->user = factory(User::class)->create([
            'organization_uuid' => $org->uuid
        ]);
        Passport::actingAs( $this->user );

        $this->collection = factory(Collection::class)->create([
            "archive_uuid" => $this->archive->uuid
        ]);

        $this->holding = factory(Holding::class)->create([
            "collection_uuid" => $this->collection->uuid
        ]);
    }

    public function test_given_an_authenticated_user_when_getting_all_holdings_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.collections.holdings.index', [ $this->archive->id, $this->collection->id ]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_holding_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.collections.holdings.show', [$this->archive->id, $this->collection->id, $this->holding->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_persisting_a_holding_it_is_stored()
    {
        $holding = [
            'title' => 'test title',
            'description' => 'test',
        ];

        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.archives.collections.holdings.store', [$this->archive->id, $this->collection->id]),
                $holding );

        $response
            ->assertStatus( 201 )
            ->assertJsonStructure( ["data" => ["id","title","description","uuid"]] );
    }

    public function test_given_an_authenticated_user_creating_a_holding_with_dublin_core_metatata_the_metadata_is_stored()
    {
        $template = ["dc" => ["subject" => "holding test subject"]];
        $holding = [
            'title' => 'test title',
            'description' => 'test',
            'defaultMetadataTemplate' =>  $template
        ];

        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.archives.collections.holdings.store', [$this->archive->id, $this->collection->id]),
                $holding );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["defaultMetadataTemplate" => $template ] );

        $response
            ->assertStatus( 201 )
            ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] )
            ->assertJsonFragment( $expected );
    }

    public function test_given_a_holding_without_dublin_core_metatata_when_adding_metadata_it_is_stored()
    {
        $template = ["dc" => ["subject" => "holding update test subject"]];
        $holdingData = [
            'title' => 'test title',
            'description' => 'test',
            'collection_uuid' => $this->collection->uuid
        ];

         $updateHoldingData = [
            'title' => 'test title',
            'description' => 'test',
            'defaultMetadataTemplate' =>  $template,
            'collection_uuid' => $this->collection->uuid
        ];

        $holding = Holding::create( $holdingData );

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.archives.collections.holdings.update', [$this->archive->id, $this->collection->id, $holding->id]),
                $updateHoldingData );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["defaultMetadataTemplate" => $template ] );

        $response
            ->assertStatus( 200 )
            ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] )
            ->assertJsonFragment( $expected );
    }

    public function test_given_an_authenticated_user_updating_holding_it_responds_200()
    {
        $holding = factory(Holding::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('admin.metadata.archives.collections.holdings.update', [$this->archive->id, $this->collection->id, $this->holding->id]),
                (new HoldingResource($holding))->toArray(null));

        $response->assertStatus( 200 );

        $this->holding->refresh();
        $this->assertEquals( $holding->title, $this->holding->title );
        $this->assertEquals( $holding->description, $this->holding->description );
    }

    public function test_given_a_holding_when_upserting_it_is_updated()
    {
        $archive = factory(Archive::class)->create();
        $collection = Collection::create(["title" => "Sweet collection title", "archive_uuid" => $archive->uuid]);
        $holding = Holding::create(["title" => "Dull Holding title", "collection_uuid" => $collection->uuid ]);
        $data = [
            "id" => $holding->id,
            "title" => "What a wonderful upsert title",
            "defaultMetadataTemplate" => ["dc" => ["title" => "The most updated novel ever!"]],
        ];
        $response = $this->actingAs( $this->user )
            ->put( route( 'admin.metadata.archives.collections.holdings.upsert', [$this->archive, $this->collection] ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }

    public function test_given_no_existing_holding_when_upserting_it_is_created()
    {
        $data = [
            "title" => "What a wonderful upsert title",
            "defaultMetadataTemplate" => ["dc" => ["title" => "The most anticipated novel ever!"]],
            "collection_uuid" => $this->collection->uuid
        ];
        $response = $this->actingAs( $this->user )
            ->put( route( 'admin.metadata.archives.collections.holdings.upsert', [$this->archive, $this->collection] ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 201 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }

}
