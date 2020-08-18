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

class MetadataTemplateControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $metadata;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $this->metadata->owner()->associate($this->user);
        $this->metadata->save();

    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.metadata-template.index') );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.metadata-template.show', [$this->metadata->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_it_responds_200()
    {
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.metadata-template.store'),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_200()
    {
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.metadata-template.update', [$this->metadata->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_delete_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.metadata-template.destroy', [$this->metadata->id]));
        $response->assertStatus( 200 );

    }

}
