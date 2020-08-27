<?php

namespace Tests\Feature;

use App\Account;
use App\Archive;
use App\ArchiveMetadata;
use App\Http\Resources\MetadataResource;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class AccountArchiveMetadataControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $metadata;
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

        $this->archive = factory(Archive::class)->create([
            "account_uuid" => $this->account->uuid,
        ]);

        $this->metadata = factory(ArchiveMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $this->metadata->parent()->associate($this->archive);
        $this->metadata->owner()->associate($this->user);
        $this->metadata->save();

    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.metadata.index', [$this->account->id, $this->archive->id]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_metadata_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.metadata.show', [$this->account->id, $this->archive->id, $this->metadata->id]) );

        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_it_responds_200()
    {
        $this->metadata->parent()->disassociate($this->archive);
        $this->metadata->save();

        $metadata = factory(ArchiveMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.account.archive.metadata.store', [$this->account->id, $this->archive->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_200()
    {
        $metadata = factory(ArchiveMetadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.account.archive.metadata.update', [$this->account->id, $this->archive->id, $this->metadata->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_delete_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE', route('api.ingest.account.archive.metadata.destroy', [$this->account->id, $this->archive->id, $this->metadata->id]));
        $response->assertStatus( 200 );

    }

}
