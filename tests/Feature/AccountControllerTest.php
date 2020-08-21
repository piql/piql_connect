<?php

namespace Tests\Feature;

use App\Http\Resources\AccountResource;
use App\Account;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class AccountControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $account;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->account = factory(Account::class)->create([
        ]);
        $this->account->owner()->associate($this->user);
        $this->account->save();

    }

    public function test_given_an_authenticated_user_when_getting_all_accounts_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.index') );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_account_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.show', [$this->account->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_storing_an_account_it_responds_200()
    {
        $account = factory(Account::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.store'),
                (new AccountResource($account))->toArray(null));

        $response->assertStatus( 200 )->assertJsonStructure(["data" => ["id"]]);
        $newAccount = Account::find($response->json("data")["id"]);

        $this->assertEquals( $account->title, $newAccount->title );
        $this->assertEquals( $account->description, $newAccount->description );


    }

    public function test_given_an_authenticated_user_updating_account_it_responds_200()
    {
        $account = factory(Account::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.account.update', [$this->account->id]),
                (new AccountResource($account))->toArray(null));

        $response->assertStatus( 200 );

        $this->account->refresh();
        $this->assertEquals( $account->title, $this->account->title );
        $this->assertEquals( $account->description, $this->account->description );
    }

    public function test_given_an_authenticated_user_when_deleting_account_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.account.destroy', [$this->account->id]));
        $response->assertStatus( 200 );

    }

}
