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
    private $holding;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->account = factory(Account::class)->create();
        $this->account->owner()->associate($this->user);
        $this->account->save();

        $this->archive = factory(Archive::class)->create([
            "account_uuid" => $this->account->uuid
        ]);

        $this->holding = factory(Holding::class)->create([
            "owner_archive_uuid" => $this->archive->uuid
        ]);
    }

    public function test_given_an_authenticated_user_when_getting_all_archives_it_responds_200()
    {

        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.accounts.archives.holdings.index', [ $this->account->id, $this->archive->id ]) );
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_and_holding_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('admin.metadata.accounts.archives.holdings.show', [$this->account->id, $this->archive->id, $this->holding->id]) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_persisting_a_holding_it_is_stored()
    {
        $holding = [
            'title' => 'test title',
            'description' => 'test',
        ];

        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.accounts.archives.holdings.store', [$this->account->id, $this->archive->id]),
                $holding );

        $response
            ->assertStatus( 201 )
            ->assertJsonStructure( ["data" => ["id","title","description","uuid"]] );
    }

    public function test_given_an_authenticated_user_creating_a_holding_with_dublin_core_metatata_the_metadata_is_stored()
    {
        $template = ["dc" => ["subject" => "holding test subject"]];
        $holding = [
            'title' => 'test title',
            'description' => 'test',
            'defaultMetadataTemplate' =>  $template
        ];

        $response = $this->actingAs( $this->user )
            ->post( route('admin.metadata.accounts.archives.holdings.store', [$this->account->id, $this->archive->id]),
                $holding );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["defaultMetadataTemplate" => $template ] );

        $response
            ->assertStatus( 201 )
            ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] )
            ->assertJsonFragment( $expected );
    }

    public function test_given_a_holding_without_dublin_core_metatata_when_adding_metadata_it_is_stored()
    {
        $template = ["dc" => ["subject" => "holding update test subject"]];
        $holdingData = [
            'title' => 'test title',
            'description' => 'test',
            'owner_archive_uuid' => $this->archive->uuid
        ];

         $updateHoldingData = [
            'title' => 'test title',
            'description' => 'test',
            'defaultMetadataTemplate' =>  $template,
            'owner_archive_uuid' => $this->archive->uuid
        ];

        $holding = Holding::create( $holdingData );

        $response = $this->actingAs( $this->user )
            ->put( route('admin.metadata.accounts.archives.holdings.update', [$this->account->id, $this->archive->id, $holding->id]),
                $updateHoldingData );

        $expected = array_replace_recursive( $response->decodeResponseJson('data'),
            ["defaultMetadataTemplate" => $template ] );

        $response
            ->assertStatus( 200 )
            ->assertJsonStructure( ["data" => ["id","title","description","uuid","defaultMetadataTemplate"]] )
            ->assertJsonFragment( $expected );
    }

    public function test_given_an_authenticated_user_updating_holding_it_responds_200()
    {
        $holding = factory(Holding::class)->create([
            'title' => 'test title',
            'description' => 'test',
        ]);

        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('admin.metadata.accounts.archives.holdings.update', [$this->account->id, $this->archive->id, $this->holding->id]),
                (new HoldingResource($holding))->toArray(null));

        $response->assertStatus( 200 );

        $this->holding->refresh();
        $this->assertEquals( $holding->title, $this->holding->title );
        $this->assertEquals( $holding->description, $this->holding->description );
    }

    public function test_given_a_holding_when_upserting_it_is_updated()
    {
        $account = factory(Account::class)->create();
        $archive = Archive::create(["title" => "Sweet Archive title", "account_uuid" => $account->uuid]);
        $holding = Holding::create(["title" => "Dull Holding title", "owner_archive_uuid" => $archive->uuid ]);
        $data = [
            "id" => $holding->id,
            "title" => "What a wonderful upsert title",
            "defaultMetadataTemplate" => ["dc" => ["title" => "The most updated novel ever!"]],
        ];
        $response = $this->actingAs( $this->user )
            ->put( route( 'admin.metadata.accounts.archives.holdings.upsert', [$this->account, $this->archive] ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }

    public function test_given_no_existing_holding_when_upserting_it_is_created()
    {
        $data = [
            "title" => "What a wonderful upsert title",
            "defaultMetadataTemplate" => ["dc" => ["title" => "The most anticipated novel ever!"]],
            "owner_archive_uuid" => $this->archive->uuid
        ];
        $response = $this->actingAs( $this->user )
            ->put( route( 'admin.metadata.accounts.archives.holdings.upsert', [$this->account, $this->archive] ),
                $data );

        $expected = array_replace_recursive(
            $response->decodeResponseJson('data'), $data );

        $response->assertStatus( 201 )
                 ->assertJsonFragment( ["data" => $expected ]);
    }



}
