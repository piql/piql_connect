<?php

namespace Tests\Feature;

use App\Http\Resources\AccountResource;
use App\Account;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Faker\Factory as faker;

class AccountControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $faker;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );
        $this->faker = Faker::create();
    }

    public function test_given_an_authenticated_user_when_getting_all_accounts_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.index') );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_account_it_responds_200()
    {
        $account = factory(Account::class)->create();
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.show', [$account->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_storing_an_account_it_is_created()
    {
        $account = factory(Account::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->post( route('api.ingest.account.store'),
                  [ $account ] );

        $response->assertStatus( 201 );
    }

    public function test_given_an_authenticated_user_storing_an_account_with_dublin_core_metadata_the_metadata_is_stored()
    {
        $metadata = [ "dc" => [
            'subject' => $this->faker->text()
        ]];

        $account = [
            'title' => $this->faker->slug(3),
            'description' => $this->faker->slug(6),
            'metadata' => $metadata
        ];

        $response = $this->actingAs( $this->user )
            ->postJson( route('api.ingest.account.store'),
                $account );

        $response->assertStatus( 201 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","metadata"]] );
        $response->assertJsonFragment( ["subject" => $metadata["dc"]["subject"]] );
    }



    public function test_given_an_authenticated_user_when_doing_partial_updates_on_an_account_it_is_updated()
    {
        $account = factory(Account::class)->create([ 'title' => 'old title']);

        $response = $this->actingAs( $this->user )
            ->patch( route('api.ingest.account.update', [$account->id]), ['title' => 'new title'] );

        $response->assertStatus( 200 )->
            assertJsonFragment(["title" => "new title"]);
        $account->refresh();
        $this->assertEquals( 'new title', $account->title );
    }

    public function test_given_an_authenticated_user_when_deleting_account_it_responds_405()
    {
        $account = factory(Account::class)->create();
        $response = $this->actingAs( $this->user )
            ->delete( route('api.ingest.account.destroy', [$account->id]) );
        $response->assertStatus( 405 );

    }

    public function test_given_an_authenticated_user_when_getting_account_metadata_it_is_in_the_response()
    {
        $account = factory(Account::class)->create(["metadata" => ["dc" => ["subject" => "testing things"]]]);
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.show', [$account->id]) );
        $response->assertStatus( 200 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","metadata"]] );
    }

    public function test_given_an_authenticated_user_when_storing_an_account_with_metadata_it_is_returned()
    {
        $title = $this->faker->slug(5);
        $metadata = ["dc" => ["title" => $title ]];
        $account = factory(Account::class)->make(["metadata" => $metadata ]);
        $response = $this->actingAs( $this->user )
            ->post( route('api.ingest.account.store'), ["metadata" => $metadata ] );
        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["metadata" => $metadata ] ); //TODO: There has to be a more elegant way...
        $response->assertStatus( 201 )
                 ->assertJsonStructure( ["data" => ["id","title","description","uuid","metadata"]] )
                 ->assertJsonFragment( ["data" => $expected ] );
    }


}
