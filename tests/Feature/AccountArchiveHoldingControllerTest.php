<?php

namespace Tests\Feature;

use App\Archive;
use App\Holding;
use App\Http\Resources\AccountResource;
use App\Account;
use App\Http\Resources\ArchiveResource;
use App\Http\Resources\HoldingResource;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class AccountArchiveHoldingControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $account;
    private $archive;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->account = factory(Account::class)->create([

        ]);
        $this->account->owner()->associate($this->user);
        $this->account->save();

        $this->archive = factory(Archive::class)->create([
            "account_uuid" => $this->account->uuid,
        ]);

        $this->holding = factory(Holding::class)->create([
            "owner_archive_uuid" => $this->archive->uuid,
        ]);

    }

    public function test_given_an_authenticated_user_when_getting_all_archives_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.holding.index', [ $this->account->id, $this->archive->id ]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_holding_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.holding.show', [$this->account->id, $this->archive->id, $this->holding->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_storing_an_holding_it_responds_200()
    {
        $newHolding = new HoldingResource(factory(Archive::class)->make([
            'title' => 'test title',
            'description' => 'test',
        ]));

        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.archive.holding.store', [$this->account->id, $this->archive->id]),
                $newHolding->toArray(null));

        $response->assertStatus( 200 )->assertJsonStructure(["data" => ["id"]]);

        $this->assertEquals(2, $this->archive->holdings()->count());
        $holding = $this->archive->holdings()->where("id", $response->json("data")["id"])->get()->first();
        $this->assertEquals( $newHolding->title, $holding->title);
        $this->assertEquals( $newHolding->description, $holding->description);
    }

    public function test_given_an_authenticated_user_updating_holding_it_responds_200()
    {
        $holding = factory(Holding::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.account.archive.holding.update', [$this->account->id, $this->archive->id, $this->holding->id]),
                (new HoldingResource($holding))->toArray(null));

        $response->assertStatus( 200 );

        $this->holding->refresh();
        $this->assertEquals( $holding->title, $this->holding->title );
        $this->assertEquals( $holding->description, $this->holding->description );
    }

    public function test_given_an_authenticated_user_when_deleting_holding_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.account.archive.holding.destroy', [$this->account->id, $this->archive->id, $this->holding->id]));
        $response->assertStatus( 200 );

    }

}
