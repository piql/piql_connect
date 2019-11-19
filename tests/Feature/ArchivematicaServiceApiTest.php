<?php

namespace Tests\Feature;

use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use App\Http\Controllers\Api\Ingest\ArchivematicaServiceController;
use Illuminate\Support\Facades\Hash;

class ArchivematicaServiceApiTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;
    private $controller;
    private $fakeApiClient;
    private $testBagName;
    private $testBagData;
    private $testBag;
    private $testService;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory(\App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $mock = new MockHandler( [new Response() ]);
        $handler = HandlerStack::create( $mock );
        $fakeApiClient = new Client( ['handler' => $handler ] );
        $this->app->instance( \GuzzleHttp\Client::class, $fakeApiClient );

        $this->testBagName = "TestBagName1";
        $this->testBagData = [
            'name' => $this->testBagName,
            'owner' => $this->testUser->id
        ];

        $testService = \App\ArchivematicaService::create([ 'url' => 'am.test', 'api_token' => Hash::make('apitoken'), 'service_type' => 'dashboard' ]);

        $this->testBag = \App\Bag::create( $this->testBagData );
    }

    public function test_when_requesting_a_list_of_archivematica_instances_it_reponds_with_200()
    {
        $response = $this->get( route('api.am.instances.index') );

        $response->assertStatus(200);
    }

    public function test_when_requesting_a_list_of_archivematica_instances_it_reponds_with_valid_json()
    {
        $response = $this->get( route('api.am.instances.index') );

        $response->assertJson([]);
    }

    public function test_when_requesting_transfer_status_from_archivematica_it_responds_with_200()
    {
        $response = $this->get( route( 'api.am.transfer.status' ) );

        $response->assertStatus(200);
    }

    public function test_when_posting_valid_a_bag_in_state_initiate_transfer_to_archivematica_it_responds_with_200()
    {
        $this->testBag->update([ 'status' => 'initiate_transfer']);
        $response = $this->json( 'POST', route( 'api.am.transfer.start', $this->testBag->id ) );

        $response->assertStatus(200);
    }

    public function test_given_a_bag_that_does_not_exist_when_posting_start_transfer_to_archivematica_it_responds_with_422()
    {
        $response = $this->json( 'POST', route( 'api.am.transfer.start', PHP_INT_MAX, [] ) );

        $response->assertStatus(422);
        $response->assertJson( ['status' => 422, 'messages' => ['bagId' => ['The selected bag id is invalid.']]]);
    }

    public function test_given_a_valid_bag_in_the_state_complete_when_posting_start_transfer_to_archivematica_it_responds_with_409()
    {
        $this->testBag->update([ 'status' => 'complete']);
        $response = $this->json( 'POST', route( 'api.am.transfer.start', $this->testBag->id ) );

        $response->assertStatus(409);
        $response->assertJson([ 'status' => 409, 'messages' => ['state' => 'The selected bag is in the wrong state. It should be [initiate_transfer] but was [complete].'] ]);
    }

    public function test_when_posting_valid_a_bag_in_state_approve_transfer_to_archivematica_it_responds_with_200()
    {
        $this->testBag->update([ 'status' => 'approve_transfer']);
        $response = $this->json( 'POST', route( 'api.am.transfer.approve', $this->testBag->id ) );

        $response->assertStatus(200);
    }

    public function test_given_a_bag_that_does_not_exist_when_posting_approve_transfer_to_archivematica_it_responds_with_422()
    {
        $response = $this->json( 'POST', route( 'api.am.transfer.approve', PHP_INT_MAX ) );

        $response->assertStatus(422);
        $response->assertJson( ['status' => 422, 'messages' => ['bagId' => ['The selected bag id is invalid.']]]);
    }

    public function test_given_a_valid_bag_in_the_state_complete_when_posting_approve_transfer_to_archivematica_it_responds_with_409()
    {
        $this->testBag->update([ 'status' => 'complete']);
        $response = $this->json( 'POST', route( 'api.am.transfer.approve', $this->testBag->id ) );

        $response->assertStatus(409);
        $response->assertJson([ 'status' => 409, 'messages' => ['state' => 'The selected bag is in the wrong state. It should be [approve_transfer] but was [complete].'] ]);
    }

    public function test_when_deleting_valid_a_bag_in_state_complete_to_archivematica_it_responds_with_200()
    {
        $this->testBag->update([ 'status' => 'complete']);
        $response = $this->json( 'DELETE', route( 'api.am.transfer.hide', $this->testBag->id ) );

        $response->assertStatus(200);
    }

    public function test_given_a_bag_that_does_not_exist_when_deleting_hide_to_archivematica_it_responds_with_422()
    {
        $response = $this->json( 'DELETE', route( 'api.am.transfer.hide', PHP_INT_MAX ) );

        $response->assertStatus(422);
        $response->assertJson( ['status' => 422, 'messages' => ['bagId' => ['The selected bag id is invalid.']]]);
    }

    public function test_given_a_valid_bag_in_the_state_approve_transfer_when_deleting_hide_transfer_to_archivematica_it_responds_with_409()
    {
        $this->testBag->update([ 'status' => 'approve_transfer']);
        $response = $this->json( 'DELETE', route( 'api.am.transfer.hide', $this->testBag->id ) );

        $response->assertStatus(409);
        $response->assertJson([ 'status' => 409, 'messages' => ['state' => 'The selected bag is in the wrong state. It should be [complete] but was [approve_transfer].'] ]);
    }

}
