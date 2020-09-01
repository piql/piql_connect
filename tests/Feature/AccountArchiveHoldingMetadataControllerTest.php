<?php

namespace Tests\Feature;

use App\Account;
use App\Archive;
use App\ArchiveMetadata;
use App\Holding;
use App\HoldingMetadata;
use App\Http\Resources\MetadataResource;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class AccountArchiveHoldingMetadataControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $metadata;
    private $account;
    private $archive;
    private $holding;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->account = factory(Account::class)->create([
        ]);
        $this->account->owner()->associate($this->user);

        $this->archive = factory(Archive::class)->create([
            "account_uuid" => $this->account->uuid,
        ]);

        $this->holding = factory(Holding::class)->create([
            "owner_archive_uuid" => $this->archive->uuid,
        ]);

        $this->metadata = factory(HoldingMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $this->metadata->parent()->associate($this->holding);
        $this->metadata->owner()->associate($this->user);
        $this->metadata->save();

    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {
        // todo: validate response
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.holding.metadata.index', [$this->account->id, $this->archive->id, $this->holding->id]) );
        $this->assertEquals(1, count($response->json("data")));
        $response->assertStatus( 200 )->assertJsonFragment($this->metadata->metadata);

    }

    public function test_given_an_authenticated_user_when_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->json('GET', route('api.ingest.account.archive.holding.metadata.show',
                [$this->account->id, $this->archive->id, $this->holding->id, $this->metadata->id]) );
        $response->assertStatus( 200 )->assertJsonFragment($this->metadata->metadata);
    }

    public function test_given_an_authenticated_user_when_storing_metadata_it_responds_200()
    {
        $this->metadata->parent()->dissociate();
        $this->metadata->save();
        $this->assertEquals(0, $this->holding->metadata()->count());
        $metadata = factory(HoldingMetadata::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best show ever!"]]
        ]);

        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.archive.holding.metadata.store', [$this->account->id, $this->archive->id, $this->holding->id]),
                (new MetadataResource($metadata))->toArray(null));
        $this->assertEquals(1, $this->holding->metadata()->count());
        $response->assertStatus( 200 )->assertJsonFragment($metadata->metadata);

    }

    public function test_given_an_authenticated_user_when_storing_additional_metadata_it_responds_409()
    {
        $this->assertEquals(1, $this->holding->metadata()->count());
        $metadata = factory(HoldingMetadata::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best show ever!"]]
        ]);

        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.archive.holding.metadata.store', [$this->account->id, $this->archive->id, $this->holding->id]),
                (new MetadataResource($metadata))->toArray(null));
        $this->assertEquals(1, $this->holding->metadata()->count());
        $response->assertStatus( 409 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_200()
    {
        $metadata = factory(HoldingMetadata::class)->create([
            "modified_by" => $this->user->id,
            "metadata" => ["dc" => ["title" => "The best show ever!"]]
        ]);
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.account.archive.holding.metadata.update', [$this->account->id, $this->archive->id, $this->holding->id, $this->metadata->id]),
                (new MetadataResource($metadata))->toArray(null));
        $this->assertEquals(1, $this->holding->metadata()->count());
        $response->assertStatus( 200 )->assertJsonFragment($metadata->metadata);
    }

    public function test_given_an_authenticated_user_when_delete_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.account.archive.holding.metadata.destroy', [$this->account->id, $this->archive->id, $this->holding->id, $this->metadata->id]));
        $this->assertEquals(0, $this->holding->metadata()->count());
        $response->assertStatus( 200 );

    }

}
