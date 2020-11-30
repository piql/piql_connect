<?php

namespace Tests\Feature;

use App\Organization;
use App\Account;
use App\AccountMetadata;
use App\ArchiveMetadata;
use App\Events\FileUploadedEvent;
use App\Events\PreProcessBagEvent;
use App\HoldingMetadata;
use App\Metadata;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use App\Bag;
use App\User;
use App\Archive;
use App\Holding;

class BagApiTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;
    private $bagTestData;
    private $testBagName1;
    private $createdBagIds;
    private $testArchive1;
    private $testArchive2;
    private $testHolding1;
    private $testHolding2;
    private $otherUser;
    private $otherBag;
    private $account;
    private $organization;
    private $accountMetadata;
    private $archiveMetadata1;
    private $archiveMetadata2;
    private $holdingMetadata1;
    private $holdingMetadata2;

    public function setUp() : void
    {
        parent::setUp();
        $this->createdBagIds = collect([]);
        $this->testBagName1 = "TestBagName1";
        $this->organization = factory(Organization::class)->create();
        $this->account = factory(Account::class)->create([ 'organization_uuid' => $this->organization->uuid ]);

        $this->testUser = User::create([
            'username' => 'BagApiTestUser',
            'password' => 'notinuse',
            'full_name' => 'BagApi TestUser',
            'email' => 'bagapitestuser@localhost',
            'organization_uuid' => $this->organization->uuid,
        ]);
        $this->testUser->organization()->associate( $this->organization );

        Passport::actingAs($this->testUser);

        $this->bagTestData = [
            'name' => $this->testBagName1,
            'owner' => $this->testUser->id
        ];

        $this->accountMetadata = factory(AccountMetadata::class)->create([
            "modified_by" => $this->testUser->id,
        ]);
        $this->accountMetadata->parent()->associate($this->account);
        $this->accountMetadata->save();
        $this->accountMetadata->owner()->associate($this->testUser);

        $this->testArchive1 = Archive::create([
            'title' => 'testBagArchive1Title',
            "account_uuid" => $this->account->uuid,
        ]);
        $this->archiveMetadata1 = factory(ArchiveMetadata::class)->create([
            "modified_by" => $this->testUser->id,
        ]);
        $this->archiveMetadata1->parent()->associate($this->testArchive1);
        $this->archiveMetadata1->save();
        $this->archiveMetadata1->owner()->associate($this->testUser);

        $this->testArchive2 = Archive::create([
            'title' => 'testBagArchive2Title',
            "account_uuid" => $this->account->uuid,
        ]);
        $this->archiveMetadata2 = factory(ArchiveMetadata::class)->create([
            "modified_by" => $this->testUser->id,
        ]);
        $this->archiveMetadata2->parent()->associate($this->testArchive2);
        $this->archiveMetadata2->save();
        $this->archiveMetadata2->owner()->associate($this->testUser);

        $this->testHolding1 = Holding::create([
            'title' => "testBagHoldingTitle1",
            'owner_archive_uuid' => $this->testArchive1->uuid
        ]);
        $this->holdingMetadata1 = factory(HoldingMetadata::class)->create([
            "modified_by" => $this->testUser->id,
        ]);
        $this->holdingMetadata1->parent()->associate($this->testHolding1);
        $this->holdingMetadata1->save();
        $this->holdingMetadata1->owner()->associate($this->testUser);

        $this->testHolding2 = Holding::create([
            'title' => "testBagHoldingTitle2",
            'owner_archive_uuid' => $this->testArchive2->uuid
        ]);
        $this->holdingMetadata2 = factory(HoldingMetadata::class)->create([
            "modified_by" => $this->testUser->id,
        ]);
        $this->holdingMetadata2->parent()->associate($this->testHolding2);
        $this->holdingMetadata2->owner()->associate($this->testUser);
        $this->holdingMetadata2->save();


        $this->bagApiResultData = ['data' => [
            'name' => $this->bagTestData['name'],
            'owner' => $this->bagTestData['owner']
        ]];

        $this->bagTestDataWithArchiveAndHolding = [
            'archive_uuid' => $this->testArchive1->uuid,
            'holding_name' => $this->testHolding1->title,
            'holding_uuid' => $this->testHolding1->uuid
        ] + $this->bagTestData;
    }

    private function createOneBag( Array $overrides = null )
    {
        $data = $this->bagTestDataWithArchiveAndHolding;
        if( $overrides ) {
            $data = $overrides + $data;
        }
        $bag = Bag::create($data);
        if( !isset( $bag->id ) ){
            throw new \Exception("Failed to create test-bag!");
        }

        $this->createdBagIds->push($bag->id);
        $bag->storage_properties->update($data);
        return $bag;
    }

    private function createExtraUserAndBags( Array $overrides = [] )
    {
        $createdBag = $this->createOneBag( $overrides );
        $secondBag = $this->createOneBag(['name' => 'secondbag'] + $overrides );

        $this->otherUser = User::create([
            'username' => 'BagApiOtherTestUser',
            'password' => 'notinuse',
            'full_name' => 'BagApi OtherTestUser',
            'email' => 'bagapiothertestuser@localhost',
            'organization_uuid' => $this->organization->uuid,
        ]);

        Passport::actingAs($this->otherUser);
        $otherBag = $this->createOneBag(['owner' => $this->otherUser->id, 'name' => 'otherbag'] + $overrides );
        $this->otherBag = $this->createOneBag(['owner' => $this->otherUser->id, 'name' => 'otherbag2'] + $overrides );
        Passport::actingAs($this->testUser);
    }

    public function test_it_can_get_a_list_of_bags()
    {
        $response = $this->get( route( 'api.ingest.bags.all' ) );

        $response->assertStatus( 200 );
    }

    public function test_when_getting_a_list_of_bags_they_are_returned_as_valid_json()
    {
        $response = $this->get( route( 'api.ingest.bags.all' ) );
        $response->assertJson([ 'data' => [] ]);
    }

    public function test_when_creating_a_bag_the_bag_is_returned_as_json()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestData );
        $response->assertStatus(200);

        $response->assertJson($this->bagApiResultData);
    }

    public function test_when_creating_a_bag_the_response_includes_storage_properties()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestData );
        $response->assertStatus(200);

        $response->assertJson(['data' => ['archive_uuid' => '', 'archive_name' => '']]);
    }

    public function test_when_creating_a_bag_with_empty_name_it_returns_200()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), [
            'name' => "",
            'owner' => $this->testUser->id
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => ""]);
    }

    public function test_when_creating_a_bag_with_invalid_name_it_returns_424()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), [
                'name' => $this->testBagName1."?",
                'owner' => $this->testUser->id
            ]);
        $response->assertStatus(424);
        $response->assertJson(['error' => 424]);
    }

    public function test_when_creating_a_bag_with_too_long_name_it_returns_424()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), [
            'name' => str_repeat("a", 65),
            'owner' => $this->testUser->id
        ]);
        $response->assertStatus(424);
        $response->assertJson(['error' => 424]);
    }


    public function test_when_creating_a_bag_given_storage_properties_are_set_the_response_includes_those_storage_properties()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding );
        $response->assertStatus(200);

        $response->assertJson(['data' => ['archive_uuid' => $this->testArchive1->uuid, 'holding_uuid' => $this->testHolding1->uuid]]);
    }

    public function test_when_updating_a_bag_given_storage_properties_are_set_the_response_includes_those_storage_properties()
    {
        $createdBagResponse = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding);

        $bagData = $createdBagResponse->getData()->data;

        $response = $this->json( 'PATCH', route( 'api.ingest.bags.update', $bagData->id ), [
            'archive_uuid' => $this->testArchive2->uuid,
            'holding_uuid' => $this->testHolding2->uuid,
            'holding_name' => $this->testHolding2->title,
        ]);

        $response->assertJson(['data' => [
            'archive_uuid' => $this->testArchive2->uuid,
            'holding_uuid' => $this->testHolding2->uuid,
            'holding_name' => $this->testHolding2->title,
        ] ]);
    }

    public function test_when_updating_a_bag_with_a_valid_name_it_returns_that_name_and_200()
    {
        $createdBagResponse = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding);

        $bagData = $createdBagResponse->getData()->data;

        $response = $this->json( 'PATCH', route( 'api.ingest.bags.update', $bagData->id ), [
            'name' => "test_bag_name",
        ]);

        $response->assertJson(['data' => [
            'name' => "test_bag_name",
        ] ]);

        $response->assertStatus( 200 );
    }

    public function test_when_updating_a_bag_with_a_invalid_name_it_returns_that_name_and_424()
    {
        $createdBagResponse = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding);

        $bagData = $createdBagResponse->getData()->data;

        $response = $this->json( 'PATCH', route( 'api.ingest.bags.update', $bagData->id ), [
            'name' => "test_bag_name?",
        ]);

        $response->assertJsonMissingExact(['data' => [
            'name' => "test_bag_name!",
        ] ]);

        $response->assertStatus( 424 );
    }

    public function test_when_updating_a_bag_with_a_too_long_name_it_returns_that_name_and_424()
    {
        $createdBagResponse = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding);

        $bagData = $createdBagResponse->getData()->data;

        $response = $this->json( 'PATCH', route( 'api.ingest.bags.update', $bagData->id ), [
            'name' => str_repeat("a", 65),
        ]);

        $response->assertJsonMissingExact(['data' => [
            'name' => "test_bag_name!",
        ] ]);

        $response->assertStatus( 424 );
    }

    public function test_when_requesting_the_latest_bag_it_responds_with_200()
    {
        $response = $this->json( 'GET', route( 'api.ingest.bags.latest' ) );
        $response->assertStatus( 200 );
    }

    public function test_when_requesting_the_lastest_bag_it_is_returned()
    {
        $createdBag = $this->createOneBag();
        $response = $this->json( 'GET', route( 'api.ingest.bags.latest' ) );
        $response->assertJson([ 'data' => $this->bagTestDataWithArchiveAndHolding ]);
    }

    public function test_when_requesting_the_lastest_bag_it_is_the_latest_bag_for_the_current_user()
    {
        $this->createExtraUserAndBags();
        $response = $this->json( 'GET', route( 'api.ingest.bags.latest' ) );
        $response->assertJson([ 'data' => $this->bagTestDataWithArchiveAndHolding ]);
    }

    public function test_when_getting_a_list_of_bags_they_are_the_bags_for_the_current_user()
    {
        $this->createExtraUserAndBags();
        $response = $this->get( route( 'api.ingest.bags.all' ) );
        $response->assertJsonFragment( ['name' => $this->testBagName1 ] );
        $response->assertJsonFragment( ['name' => 'secondbag' ] );
        $response->assertJsonMissing( ['name' => 'otherbag' ] );
        $response->assertJsonMissing( ['name' => 'otherbag2' ] );
    }

    public function test_when_getting_a_list_of_bags_in_processing_they_are_the_bags_for_the_current_user()
    {
        $this->createExtraUserAndBags([ 'status' => 'ingesting' ]);
        $response = $this->get( route( 'api.ingest.bags.processing' ) );
        $response->assertJsonFragment( ['name' => $this->testBagName1 ] );
        $response->assertJsonFragment( ['name' => 'secondbag' ] );
        $response->assertJsonMissing( ['name' => 'otherbag' ] );
        $response->assertJsonMissing( ['name' => 'otherbag2' ] );
    }

    public function test_when_getting_a_list_of_bags_in_complete_they_are_the_bags_for_the_current_user()
    {
        $this->createExtraUserAndBags([ 'status' => 'complete' ]);
        $response = $this->get( route( 'api.ingest.bags.complete' ) );
        $response->assertJsonFragment( ['name' => $this->testBagName1 ] );
        $response->assertJsonFragment( ['name' => 'secondbag' ] );
        $response->assertJsonMissing( ['name' => 'otherbag' ] );
        $response->assertJsonMissing( ['name' => 'otherbag2' ] );
    }


    public function test_it_can_get_a_bag_by_id()
    {
        $createdBag = $this->createOneBag();
        $response = $this->get( route( 'api.ingest.bags.show', $createdBag->id ) );
        $response->assertJson([ 'data' => $this->bagTestDataWithArchiveAndHolding ]);
    }

    public function test_it_throws_401_when_requesting_a_bag_belonging_to_another_user()
    {
        $this->createExtraUserAndBags();
        $response = $this->get( route( 'api.ingest.bags.show', $this->otherBag->id ) );
        $response->assertStatus( 401 );
        $response->assertJson([ 'error' => 401, 'message' => 'The current user is not authorized to access bag with id '.$this->otherBag->id ]);
    }

    public function test_it_throws_401_when_updating_a_bag_belonging_to_another_user()
    {
        $this->createExtraUserAndBags();

        $response = $this->patch( route( 'api.ingest.bags.update', $this->otherBag->id ), [
            'archive_name' => 'whatever'
        ]);

        $response->assertStatus( 401 );
        $response->assertJson([ 'error' => 401, 'message' => 'The current user is not authorized to update bag with id '.$this->otherBag->id ]);
    }

    public function test_commit_bag_and_it_returns_200()
    {
        $createdBag = $this->createOneBag();

        Event::fake();
        $response = $this->post( route( 'api.ingest.bags.commit', $createdBag->id ) );
        $response->assertStatus( 200 );
        Event::assertNotDispatched( PreProcessBagEvent::class );
        $uuid = $createdBag->uuid;

        /* TODO: Assert metadata on the bag
        $query = AccountMetadata::whereHasMorph('parent', [\App\Bag::class], function(Builder $query) use ($uuid) {
            $query->where("uuid", $uuid);
        });
        $this->assertEquals(1, $query->count());

        $query = ArchiveMetadata::whereHasMorph('parent', [\App\Bag::class], function(Builder $query) use ($uuid) {
            $query->where("uuid", $uuid);
        });
        $this->assertEquals(1, $query->count());

        $query = HoldingMetadata::whereHasMorph('parent', [\App\Bag::class], function(Builder $query) use ($uuid) {
            $query->where("uuid", $uuid);
        });
        $this->assertEquals(1, $query->count());
         */
    }

    public function test_commit_bag_without_a_empty_name_and_it_returns_424()
    {
        $createdBag = $this->createOneBag(["name" => ""]);
        Event::fake();
        $response = $this->post( route( 'api.ingest.bags.commit', $createdBag->id ) );
        $response->assertStatus( 424 );
        Event::assertNotDispatched( PreProcessBagEvent::class );
    }

    public function test_commit_bag_without_a_valid_name_and_it_returns_424()
    {
        $createdBag = $this->createOneBag(["name" => "abc?"]);
        Event::fake();
        $response = $this->post( route( 'api.ingest.bags.commit', $createdBag->id ) );
        $response->assertStatus( 424 );
        Event::assertNotDispatched( PreProcessBagEvent::class );
    }

    public function test_commit_bag_with_empty_name_and_it_returns_424()
    {
        $createdBag = $this->createOneBag(["name" => ""]);
        Event::fake();
        $response = $this->post( route( 'api.ingest.bags.commit', $createdBag->id ) );
        $response->assertStatus( 424 );
        Event::assertNotDispatched( PreProcessBagEvent::class );
    }

}
