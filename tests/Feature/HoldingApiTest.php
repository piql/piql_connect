<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;
use Faker\Factory as Faker;
use App\Holding;

class HoldingApiTest extends TestCase
{
    private $title1;
    private $description1;
    private $uuid1;
    private $validHoldingData;
    private $validHoldingDataCreated;
    private $createdHoldingIds;
    private $holdingCount;
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $this->createdHoldingIds = collect([]);
        $this->holdingCount = Holding::count();
        $faker = Faker::create();
        $this->title1 = $faker->company();
        $this->uuid1 = $faker->uuid();
        $this->description1 = $faker->text(100);
        $this->title2 = $faker->company();
        $this->uuid2 = $faker->uuid();
        $this->description2 = $faker->text(100);
        $this->validHoldingDataCreated = [
            'uuid' => $this->uuid1,
            'title' => $this->title1,
            'description' => $this->description1
        ];

        $this->validHoldingData = [
            'uuid' => $this->uuid2,
            'title' => $this->title2,
            'description' => $this->description2
        ];

        $this->createdTestHolding = Holding::create($this->validHoldingDataCreated);

        $this->createdHoldingIds->push( $this->createdTestHolding->id );
    }

    private function markForRemoval($response)
    {
        try {
            $this->createdHoldingIds->push( $response->getOriginalContent()->id );
        }
        catch(\Exception $ex)
        {
            print("\nWarning: A holding could not be marked for removal!\n");
        }
    }

    public function tearDown() : void
    {
        $this->testUser->delete();

        $this->createdHoldingIds->map( function ($createdId) {
            $holding = Holding::find($createdId);
            if($holding){
                $holding->delete();
            }
        });
        $this->assertEquals($this->holdingCount, Holding::count());
        parent::tearDown();
    }

    public function test_it_can_get_a_list_of_holdings()
    {
        $response = $this->get( route( 'planning.holdings.index' ) );
        $response->assertStatus( 200 );
    }

    public function test_when_requesting_a_list_of_holdings_it_returns_a_valid_json_array()
    {
        $response = $this->get(route('planning.holdings.index'));
        $response->assertJsonStructure(['data' => []]);
    }

    public function test_when_creating_a_holding_given_valid_data_it_responds_with_201()
    {
        $response = $this->json('POST',
            route('planning.holdings.store'),
            $this->validHoldingData);
        $response->assertStatus(201);
        $this->markForRemoval($response);
    }

    public function test_when_creating_a_holding_given_valid_data_it_returns_a_valid_json_object()
    {
        $response = $this->json( 'POST',
            route( 'planning.holdings.store' ),
            $this->validHoldingData );
        $this->markForRemoval( $response );

        $response->assertJson([ 'data' => $this->validHoldingData ]);
    }

    public function test_given_a_holding_exists_when_deleting_it_it_is_soft_deleted()
    {
        $holdingId = $this->createdTestHolding->id;
        $response = $this->json( 'DELETE',
            route( 'planning.holdings.destroy', ['id' => $holdingId] )
        );
        $response->assertStatus(204);
        $model = Holding::withTrashed()->find($holdingId);
        $this->assertNotNull($model->deleted_at);
    }

    public function test_given_a_holding_does_not_exist_when_deleting_it_responds_with_404_and_error_message()
    {
        $holdingId = PHP_INT_MAX;
        $response = $this->json( 'DELETE',
            route( 'planning.holdings.destroy', ['id' => $holdingId] )
        );

        $response->assertStatus(404);
        $response->assertJson(['error' => 'HOLDING WITH ID '.$holdingId.' NOT FOUND']); 
    }

    public function test_given_a_holding_exists_when_requesting_it_it_is_returned()
    {
        $holdingId = $this->createdTestHolding->id;
        $response = $this->json( 'GET',
            route( 'planning.holdings.show', ['id' => $holdingId] )
        );

        $response->assertStatus(200);
        $response->assertJson([ 'data' => $this->validHoldingDataCreated ]);
    }

    public function test_given_a_holding_does_not_exist_when_requesting_it_responds_with_a_404_and_error_message()
    {
        $holdingId = PHP_INT_MAX;
        $response = $this->json( 'GET',
            route( 'planning.holdings.show', ['id' => $holdingId] )
        );

        $response->assertStatus(404);
        $response->assertJson(['error' => 'HOLDING WITH ID '.$holdingId.' NOT FOUND']); 
    }
}
