<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use App\Bag;
use App\User;
use App\Holding;
use App\Fonds;

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

    public function setUp() : void
    {
        parent::setUp();
        $this->createdBagIds = collect([]);
        $this->testBagName1 = "TestBagName1";
        $this->testUser = User::create([
            'username' => 'BagApiTestUser', 
            'password' => 'notinuse',
            'full_name' => 'BagApi TestUser',
            'email' => 'bagapitestuser@localhost'
        ]);

        Passport::actingAs($this->testUser);

        $this->bagTestData = [ 
            'name' => $this->testBagName1,
            'owner' => $this->testUser->id
        ];

        $this->testArchive1 = Holding::create(['title' => 'testBagArchive1Title']);
        $this->testArchive2 = Holding::create(['title' => 'testBagArchive2Title']);
        $this->testHolding1 = Fonds::create([
            'title' => "testBagHoldingTitle1",
            'owner_holding_uuid' => $this->testArchive1->uuid
        ]);
        $this->testHolding2 = Fonds::create([
            'title' => "testBagHoldingTitle2",
            'owner_holding_uuid' => $this->testArchive2->uuid
        ]);


        $this->bagApiResultData = ['data' => [
            'name' => $this->bagTestData['name'],
            'owner' => $this->bagTestData['owner']
        ]];

        $this->bagTestDataWithArchiveAndHolding = [
            'archive_uuid' => $this->testArchive1->uuid,
            'holding_name' => $this->testHolding1->title
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
        $this->otherUser = User::create([
            'username' => 'BagApiOtherTestUser',
            'password' => 'notinuse',
            'full_name' => 'BagApi OtherTestUser',
            'email' => 'bagapiothertestuser@localhost'
        ]);

        $createdBag = $this->createOneBag( $overrides );
        $secondBag = $this->createOneBag(['name' => 'secondbag'] + $overrides );
        $otherBag = $this->createOneBag(['owner' => $this->otherUser->id, 'name' => 'otherbag'] + $overrides );
        $this->otherBag = $this->createOneBag(['owner' => $this->otherUser->id, 'name' => 'otherbag2'] + $overrides );
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

        $response->assertJson(['data' => ['archive_uuid' => '', 'holding_name' => '']]);
    }

    public function test_when_creating_a_bag_given_storage_properties_are_set_the_response_includes_those_storage_properties()
    {
        $response = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding );
        $response->assertStatus(200);

        $response->assertJson(['data' => ['archive_uuid' => $this->testArchive1->uuid, 'holding_name' => $this->testHolding1->title]]);
    }

    public function test_when_updating_a_bag_given_storage_properties_are_set_the_response_includes_those_storage_properties()
    {
        $createdBagResponse = $this->json( 'POST', route( 'api.ingest.bags.store' ), $this->bagTestDataWithArchiveAndHolding);

        $bagData = $createdBagResponse->getData()->data;

        $response = $this->json( 'PATCH', route( 'api.ingest.bags.update', $bagData->id ), [
            'archive_uuid' => $this->testArchive2->uuid,
            'holding_name' => $this->testHolding2->title
        ]);
        
        $response->assertJson(['data' => [
            'archive_uuid' => $this->testArchive2->uuid, 
            'holding_name' => $this->testHolding2->title
        ] ]);
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
            'holding_name' => 'whatever'
        ]);

        $response->assertStatus( 401 );
        $response->assertJson([ 'error' => 401, 'message' => 'The current user is not authorized to update bag with id '.$this->otherBag->id ]);
    }

}
