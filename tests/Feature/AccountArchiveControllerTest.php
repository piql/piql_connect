<?php

namespace Tests\Feature;

use App\Archive;
use App\Http\Resources\AccountResource;
use App\Account;
use App\Http\Resources\ArchiveResource;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class AccountArchiveControllerTest extends TestCase
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

    }

    public function test_given_an_authenticated_user_when_getting_all_archives_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.index', [ $this->account->id ]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_archive_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.show', [$this->account->id, $this->archive->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_storing_an_archive_it_responds_200()
    {
        $newArchive = new ArchiveResource(factory(Archive::class)->make([
            'title' => 'test title',
            'description' => 'test',
        ]));

        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.archive.store', [$this->account->id]),
                (new ArchiveResource($newArchive))->toArray(null));

        $response->assertStatus( 200 )->assertJsonStructure(["data" => ["id"]]);

        $this->assertEquals(2, $this->account->archives()->count());
        $archive = $this->account->archives()->where("id", $response->json("data")["id"])->get()->first();
        $this->assertEquals( $newArchive->title, $archive->title);
        $this->assertEquals( $newArchive->description, $archive->description);
    }

    public function test_given_an_authenticated_user_updating_archive_it_responds_200()
    {
        $archive = factory(Archive::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.account.archive.update', [$this->account->id, $this->archive->id]),
                (new ArchiveResource($archive))->toArray(null));

        $response->assertStatus( 200 );

        $this->archive->refresh();
        $this->assertEquals( $archive->title, $this->archive->title );
        $this->assertEquals( $archive->description, $this->archive->description );
    }

    public function test_given_an_authenticated_user_when_deleting_archive_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.account.archive.destroy', [$this->account->id, $this->archive->id]));
        $response->assertStatus( 204 );

    }

}
