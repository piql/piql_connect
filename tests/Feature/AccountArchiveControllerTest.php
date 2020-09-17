<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Faker\Factory as faker;
use App\Account;
use App\Archive;
use App\User;
use App\Http\Resources\AccountResource;
use App\Http\Resources\ArchiveResource;

class AccountArchiveControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $account;
    private $archive;
    private $archiveContent;
    private $faker;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );
        $this->faker = Faker::create();

        $this->account = factory(Account::class)->create([

        ]);
        $this->account->owner()->associate($this->user);
        $this->account->save();

        $this->archiveContent = [ "account_uuid" => $this->account->uuid ];

        $this->archive = factory(Archive::class)->create( $this->archiveContent );

    }

    public function test_given_an_authenticated_user_when_getting_all_archives_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.index', [ $this->account->id ]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_requesting_an_archive_it_is_returned()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.show', [$this->account->id, $this->archive->id]) );
        $response->assertStatus( 200 )
            ->assertJsonFragment( $this->archiveContent );
    }

    public function test_given_an_authenticated_user_storing_an_archive_it_is_stored()
    {
        $archive = [
            'title' => $this->faker->slug(3),
            'description' => $this->faker->slug(6)
        ];

        $response = $this->actingAs( $this->user )
            ->postJson( route('api.ingest.account.archive.store', [$this->account->id]),
                $archive );

        $response->assertStatus( 200 )
                 ->assertJsonStructure(["data" => ["id"]])
                 ->assertJsonFragment( $archive );
        $this->assertEquals(2, $this->account->archives()->count());
    }

    public function test_given_an_authenticated_user_storing_an_archive_with_dublin_core_metadata_the_metadata_is_stored()
    {
        $metadata = [ "dc" => [
            'subject' => $this->faker->text()
        ]];

        $archive = [
            'title' => $this->faker->slug(3),
            'description' => $this->faker->slug(6),
            'metadata' => $metadata
        ];

        $response = $this->actingAs( $this->user )
            ->postJson( route('api.ingest.account.archive.store', [$this->account->id]),
                $archive );

        $response->assertStatus( 200 )
                 ->assertJsonStructure(["data" => ["id"]])
                 ->assertJsonFragment( ["subject" => $metadata["dc"]["subject"]] );
        $this->assertEquals(2, $this->account->archives()->count());
    }


    public function test_given_an_authenticated_user_updating_when_updating_an_archive_it_is_updated()
    {
        $archive = [
            'title' => 'updated title',
            'description' => 'updated description',
        ];

        $response = $this->actingAs( $this->user )
                         ->patchJson(
                             route( 'api.ingest.account.archive.update',
                             [$this->account->id, $this->archive->id] ),
                             $archive );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( $archive );
    }

    public function test_given_an_archive_when_updating_it_with_dublin_core_metadata_it_is_updated()
    {
        $metadata = [ "dc" => [
            'subject' => $this->faker->text()
        ]];

        $existing = Archive::find( $this->archive->id )->metadata["dc"];
        $expected = ["metadata" => ["dc" => $metadata["dc"] + $existing ]];

        $response = $this->actingAs( $this->user )
                         ->patchJson(
                             route( 'api.ingest.account.archive.update',
                             [$this->account->id, $this->archive->id] ),
                             ["metadata" => $metadata ] );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( $expected );
    }


    public function test_given_an_authenticated_user_when_deleting_archive_it_responds_405()
    {
        $response = $this->actingAs( $this->user )
            ->delete( route('api.ingest.account.archive.destroy', [$this->account->id, $this->archive->id]));
        $response->assertStatus( 405 );
    }

    public function test_given_an_authenticated_user_when_getting_an_archives_they_have_metadata()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.index', [$this->account->id]) );
        $response->assertStatus( 200 );
        //$decoded = collect( json_decode( $response->getContent() )->data )->firstWhere('id', $this->archive->id);
        $archives = collect( json_decode( $response->getContent() )->data );
        $decoded = $archives->first();
        $this->assertTrue( $archives->count() > 0 );
        $this->assertNotNull( $decoded->metadata );
    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_with_updated_data()
    {
        $metadata = [
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('api.ingest.account.archive.update', [$this->account->id, $this->archive->id]),
               $metadata );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'), $metadata );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( $expected );
    }

}
