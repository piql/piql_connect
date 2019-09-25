<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use App\Bag;
use App\User;
use App\Holding;
use App\Fonds;

class BagApiTest extends TestCase
{
    private $testUser;
    private $bagTestData;
    private $testBagName1;
    private $createdBagIds;
    private $testArchive1;
    private $testArchive2;
    private $testHolding1;
    private $testHolding2;

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

    public function tearDown() : void
    {
        $this->createdBagIds->map( function($bagId) {
            $b = Bag::find($bagId);
            if($b){
                $b->delete();
            }
        });

        $this->testUser->delete();
        $this->testHolding1->delete();
        $this->testArchive1->delete();
        $this->testHolding2->delete();
        $this->testArchive2->delete();
        parent::tearDown();
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

    private function markForRemoval($response)
    {
        if($response->status() == 200)
        {
            try {
                $this->createdBagIds->push(json_decode($response->getData()->data->id));
            }
            catch(\Exception $ex)
            {
                echo($ex);
                throw $ex;
            }
        }
    }


    public function test_it_can_get_a_list_of_bags()
    {
        $response = $this->get('/api/v1/ingest/bags');

        $response->assertStatus(200);
    }

    public function test_when_getting_a_list_of_bags_they_are_returned_as_valid_json()
    {
        $response = $this->get('/api/v1/ingest/bags');
        $response->assertJson(['data' => []]);
    }

    public function test_when_creating_a_bag_the_bag_is_returned_as_json()
    {
        $response = $this->json('POST', '/api/v1/ingest/bags', $this->bagTestData);
        $response->assertStatus(200);
        $this->markForRemoval($response);

        $response->assertJson($this->bagApiResultData);
    }

    public function test_when_creating_a_bag_the_response_includes_storage_properties()
    {
        $response = $this->json('POST', '/api/v1/ingest/bags', $this->bagTestData);
        $response->assertStatus(200);
        $this->markForRemoval($response);

        $response->assertJson(['data' => ['archive_uuid' => '', 'holding_name' => '']]);
    }

    public function test_when_creating_a_bag_given_storage_properties_are_set_the_response_includes_those_storage_properties()
    {
        $response = $this->json('POST', '/api/v1/ingest/bags', $this->bagTestDataWithArchiveAndHolding);
        $this->markForRemoval($response);
        $response->assertStatus(200);

        $response->assertJson(['data' => ['archive_uuid' => $this->testArchive1->uuid, 'holding_name' => $this->testHolding1->title]]);
    }

    public function test_when_updating_a_bag_given_storage_properties_are_set_the_response_includes_those_storage_properties()
    {
        $createdBagResponse = $this->json('POST', '/api/v1/ingest/bags', $this->bagTestDataWithArchiveAndHolding);
        $this->markForRemoval($createdBagResponse);

        $bagData = $createdBagResponse->getData()->data;

        $response = $this->json('PATCH', '/api/v1/ingest/bags/'.$bagData->id, [
            'archive_uuid' => $this->testArchive2->uuid,
            'holding_name' => $this->testHolding2->title
        ]);
        
        $response->assertJson(['data' => [
            'archive_uuid' => $this->testArchive2->uuid, 
            'holding_name' => $this->testHolding2->title
        ]]);
    }

    public function test_when_requesting_the_latest_bag_it_responds_with_200()
    {
        $response = $this->json('GET', '/api/v1/ingest/bags/latest');
        $response->assertStatus(200);
    }

    public function test_when_requesting_the_lastest_bag_it_is_returned()
    {
        $createdBag = $this->createOneBag();
        $response = $this->json('GET', '/api/v1/ingest/bags/latest');
        $response->assertJson([ 'data' => $this->bagTestDataWithArchiveAndHolding ]);
    }

    public function test_a_query_for_offline_bag_returns_only_bags_with_the_status_complete()
    {
        $this->markTestSkipped("need tests for the entire piql-it workflow!");
    }


}
