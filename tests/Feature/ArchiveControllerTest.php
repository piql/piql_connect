<?php

namespace Tests\Feature;

use App\Http\Resources\ArchiveResource;
use App\Organization;
use App\Archive;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Faker\Factory as faker;

class ArchiveControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $organization;
    private $user;
    private $faker;

    public function setUp() : void
    {
        parent::setUp();

        $this->organization = factory(Organization::class)->create();
        factory(Archive::class)->create(['organization_uuid' => $this->organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $this->organization->uuid]);

        Passport::actingAs( $this->user );
        $this->faker = Faker::create();
    }

    public function test_given_an_authenticated_user_when_getting_all_archives_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.index') );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_archive_it_responds_200()
    {
        $archive = factory(Archive::class)->create(['organization_uuid' => $this->organization->uuid]);
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.show', [$archive->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_storing_an_archive_it_is_created()
    {
        static::markTestSkipped('Currently, there is a one-to-one relationship between organization and archive. Skipped until multiple archives are implemented.');

        $archive = factory(Archive::class)->create([
            'title' => 'test title',
            'description' => 'test',
            'organization_uuid' => $this->organization->uuid
        ]);

        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.archives.store'),
                  [ $archive ] );

        $response->assertStatus( 201 );
    }

    public function test_given_an_authenticated_user_storing_an_archive_with_dublin_core_metadata_the_metadata_is_stored()
    {
        $metadataTemplate = [ "dc" => [
            'subject' => $this->faker->text()
        ]];

        $archive = [
            'title' => $this->faker->slug(3),
            'description' => $this->faker->slug(6),
            'organization_uuid' => $this->organization->uuid,
            'defaultMetadataTemplate' => $metadataTemplate
        ];

        $response = $this->actingAs( $this->user )
            ->postJson( route('admin.metadata.archives.store'),
                $archive );

        $response->assertStatus( 201 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] );
        $response->assertJsonFragment( ["subject" => $metadataTemplate["dc"]["subject"]] );
    }



    public function test_given_an_authenticated_user_when_doing_partial_updates_on_an_archive_it_is_updated()
    {
        $archive = factory(Archive::class)->create([
            'title' => 'old title',
            'organization_uuid' => $this->organization->uuid]);

        $response = $this->actingAs( $this->user )
            ->patch( route('admin.metadata.archives.update', [$archive->id]), ['title' => 'new title'] );

        $response->assertStatus( 200 )->
            assertJsonFragment(["title" => "new title"]);
        $archive->refresh();
        $this->assertEquals( 'new title', $archive->title );
    }

    public function test_given_an_authenticated_user_when_deleting_archive_it_responds_405()
    {
        $archive = factory(Archive::class)->create(['organization_uuid' => $this->organization->uuid]);
        $response = $this->actingAs( $this->user )
            ->delete( route('admin.metadata.archives.destroy', [$archive->id]) );
        $response->assertStatus( 405 );

    }

    public function test_given_an_authenticated_user_when_getting_archive_metadata_it_is_in_the_response()
    {
        $archive = factory(Archive::class)->create([
            "defaultMetadataTemplate" => ["dc" => ["subject" => "testing things"]],
            'organization_uuid' => $this->organization->uuid]);
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.archives.show', [$archive->id]) );
        $response->assertStatus( 200 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] );
    }

    public function test_given_an_authenticated_user_when_storing_an_archive_with_metadata_it_is_returned()
    {
        $title = $this->faker->slug(5);
        $metadataTemplate = ["dc" => ["title" => $title ]];
        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.archives.store'), [
                "defaultMetadataTemplate" => $metadataTemplate,
                'organization_uuid' => $this->organization->uuid ]);
        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["defaultMetadataTemplate" => $metadataTemplate ] ); //TODO: There has to be a more elegant way...
        $response->assertStatus( 201 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] )
                 ->assertJsonFragment( ["data" => $expected ] );
    }

    public function test_given_an_authenticated_user_when_updating_an_archive_that_does_not_exist_it_is_upserted()
    {
        $data = [
            "defaultMetadataTemplate" => ["dc" => ["title" => "The best novel ever!"]],
            'organization_uuid' => $this->organization->uuid
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.archives.upsert' ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 201 )
            ->assertJsonFragment( $expected );
    }

    public function test_given_an_authenticated_user_when_upserting_an_archive_that_exists_it_is_updated()
    {
        $archive = factory(Archive::class)->create(['organization_uuid' => $this->organization->uuid]);

        $data = [
            "id" => $archive->id,
            "defaultMetadataTemplate" => ["dc" => ["title" => "The most updated novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route( 'admin.metadata.archives.upsert' ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }


}
