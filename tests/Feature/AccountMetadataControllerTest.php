<?php

namespace Tests\Feature;

use App\Account;
use App\Http\Resources\MetadataResource;
use App\AccountMetadata;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class AccountMetadataControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $metadata;
    private $account;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->account = factory(Account::class)->create([
        ]);
        $this->account->owner()->associate($this->user);

        $this->metadata = factory(AccountMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $this->metadata->parent()->associate($this->account);
        $this->metadata->owner()->associate($this->user);
        $this->metadata->save();

    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {
        $this->metadata->parent()->disassociate($this->account);
        $this->metadata->save();

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.metadata.index', [$this->account->id]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.metadata.show', [$this->account->id, $this->metadata->id]) );

        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_it_responds_200()
    {
        $metadata = factory(AccountMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.metadata.store', [$this->account->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_200()
    {
        $metadata = factory(AccountMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.account.metadata.update', [$this->account->id, $this->metadata->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_delete_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.account.metadata.destroy', [$this->account->id, $this->metadata->id]));
        $response->assertStatus( 200 );

    }

}
