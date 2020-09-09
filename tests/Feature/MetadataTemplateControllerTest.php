<?php

namespace Tests\Feature;

use App\Http\Resources\MetadataResource;
use App\MetadataTemplate;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

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

        $this->metadata = factory(MetadataTemplate::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best show ever!"]]
        ]);
        $this->metadata->owner()->associate($this->user);
        $this->metadata->save();

    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->json('GET', route('api.ingest.metadata-template.index') );
        $this->assertEquals(1, count($response->json("data")));
        $response->assertStatus( 200 )->assertJsonFragment($this->metadata->metadata);

    }

    public function test_given_an_authenticated_user_when_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('GET', route('api.ingest.metadata-template.show', [$this->metadata->id]) );
        $response->assertStatus( 200 )->assertJsonFragment($this->metadata->metadata);
    }

    public function test_given_an_authenticated_user_when_storing_metadata_it_responds_200()
    {
        $metadata = factory(MetadataTemplate::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ]);
        $this->assertEquals(1, \auth()->user()->morphMany( MetadataTemplate::class,'owner')->count());
        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.metadata-template.store'),
                (new MetadataResource($metadata))->toArray(null));
        $this->assertEquals(2, \auth()->user()->morphMany( MetadataTemplate::class,'owner')->count());
        $response->assertStatus( 200 )->assertJsonFragment($metadata->metadata);

    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_200()
    {
        $metadata = factory(MetadataTemplate::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ]);
        $this->assertEquals(1, \auth()->user()->morphMany( MetadataTemplate::class,'owner')->count());
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.metadata-template.update', [$this->metadata->id]),
                (new MetadataResource($metadata))->toArray(null));
        $this->assertEquals(1, \auth()->user()->morphMany( MetadataTemplate::class,'owner')->count());
        $response->assertStatus( 200 )->assertJsonFragment($metadata->metadata);
    }

    public function test_given_an_authenticated_user_when_delete_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.metadata-template.destroy', [$this->metadata->id]));
        $this->assertEquals(0, \auth()->user()->morphMany( MetadataTemplate::class,'owner')->count());
        $response->assertStatus( 204 );

    }

}
