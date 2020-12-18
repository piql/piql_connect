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

        $organization = factory(\App\Organization::class)->create();
        $archive = factory(\App\Archive::class)->create(['organization_uuid' => $organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $organization->uuid]);
        Passport::actingAs( $this->user );

        $this->metadata = factory(MetadataTemplate::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best show ever!"]]
        ]);
        $this->metadata->owner()->associate($archive);
        $this->metadata->save();
    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_templates_it_returns_a_list_of_templates()
    {

        $response = $this->actingAs( $this->user )
            ->json('GET', route('admin.metadata.templates.index') );
        $response->assertStatus( 200 )
                 ->assertJsonStructure(["data" => []] );
    }

    public function test_given_an_authenticated_user_when_getting_a_metadata_template_it_returns_the_template()
    {
        $response = $this->actingAs( $this->user )
            ->json( 'GET', route( 'admin.metadata.templates.show', [$this->metadata] ) );
        $response
            ->assertStatus( 200 )
            ->assertJsonFragment( $this->metadata->metadata );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_it_is_created()
    {
        $metadata = [
            "title" => "The title of the metadata of the best novel ever!",
            "description" => "ello guvnor",
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ];
        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.templates.store'), $metadata );
        $response->assertStatus( 201 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_is_updated()
    {
        $metadata = [
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.templates.update', [$this->metadata->id]),
                $metadata );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_delete_metadata_it_responds_204()
    {
        $response = $this->actingAs( $this->user )
            ->delete( route('admin.metadata.templates.destroy', [$this->metadata->id]));
        $response->assertStatus( 204 );

    }

    public function test_given_an_authenticated_user_when_updating_a_template_that_does_not_exist_it_is_upserted()
    {
        $data = [
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.templates.upsert' ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 201 )
            ->assertJsonFragment( $expected );
    }

    public function test_given_an_authenticated_user_when_upserting_a_template_that_exists_it_is_updated()
    {
        $existingTemplateId = $this->metadata->id;

        $data = [
            "id" => $existingTemplateId,
            "metadata" => ["dc" => ["title" => "The most updated novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route( 'admin.metadata.templates.upsert' ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }

}
