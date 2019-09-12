<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use App\Bag;
use App\User;
use App\Holding;
use App\Fonds;

class BagApiTest extends TestCase
{
    private $testUser;
    private $bagTestData;
    private $createdBagIds;
    private $testArchive1;
    private $testArchive2;
    private $testHolding1;
    private $testHolding2;

    public function setUp() : void
    {
        parent::setUp();
        $this->createdBagIds = collect([]);
        $this->testUser = User::create([
            'username' => 'BagApiTestUser', 
            'password' => 'notinuse',
            'full_name' => 'BagApi TestUser',
            'email' => 'bagapitestuser@localhost'
        ]);

        $this->bagTestData = [ 
            'bagName' => 'BagApiTestName',
            'userId' => $this->testUser->id
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
            'name' => $this->bagTestData['bagName'],
            'owner' => $this->bagTestData['userId']
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

    private function markForRemoval($response)
    {
        $this->createdBagIds->push(json_decode($response->getData()->data->id));
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
        $this->markForRemoval($response);

        $response->assertJson($this->bagApiResultData);
    }

    public function test_when_creating_a_bag_the_response_includes_storage_properties()
    {
        $response = $this->json('POST', '/api/v1/ingest/bags', $this->bagTestData);
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




}
