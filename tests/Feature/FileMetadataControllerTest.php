<?php

namespace Tests\Feature;

use App\Bag;
use App\File;
use App\Http\Resources\MetadataResource;
use App\Metadata;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;

class FileMetadataControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $file;
    private $bag;

    public function setUp() : void
    {
        parent::setUp();

        $organization = factory(\App\Organization::class)->create();
        factory(\App\Archive::class)->create(['organization_uuid' => $organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $organization->uuid]);

        Passport::actingAs( $this->user );

        $bag = factory(Bag::class)->create([
            "status" => "open"
        ]);

        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);

        $this->file = factory(File::class)->create([
            "bag_id" => $bag->id,
        ]);

        $this->file->metadata()->save($metadata);

        $this->bag = $bag->fresh();
    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.files.metadata.index', $this->file->id) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.files.metadata.show', [$this->file->id, $this->file->metadata[0]->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_on_a_closed_bag_it_responds_400()
    {
        $this->bag->status="closed";
        $this->bag->save();
        $response = $this->actingAs( $this->user )
            ->post( route('api.ingest.files.metadata.store', $this->file->id));
        $response->assertStatus( 400 );

    }

    public function test_given_an_authenticated_user_when_storing_metadata_on_a_open_bag_it_responds_200()
    {
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.files.metadata.store', $this->file->id),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_on_a_open_bag_it_responds_200()
    {
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.files.metadata.update', [$this->file->id, $this->file->metadata[0]->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_on_a_closed_bag_it_responds_400()
    {
        $this->bag->status="closed";
        $this->bag->save();
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.files.metadata.update', [$this->file->id, $this->file->metadata[0]->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 400 );

    }

    public function test_given_an_authenticated_user_when_delete_metadata_on_a_open_bag_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.files.metadata.destroy', [$this->file->id, $this->file->metadata[0]->id]));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_delete_metadata_on_a_closd_bag_it_responds_400()
    {
        $this->bag->status="closed";
        $this->bag->save();
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.files.metadata.destroy', [$this->file->id, $this->file->metadata[0]->id]));
        $response->assertStatus( 400 );

    }
}
