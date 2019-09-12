<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Webpatser\Uuid\Uuid;
use Faker\Factory as Faker;
use App\Holding;

class HoldingApiTest extends TestCase
{
    private $title1;
    private $description1;
    private $uuid1;
    private $validHoldingData;
    private $createdHoldingIds;
    private $holdingCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->createdHoldingIds = collect([]);
        $this->holdingCount = Holding::count();
        $faker = Faker::create();
        $this->title1 = $faker->company();
        $this->uuid1 = $faker->uuid();
        $this->description1 = $faker->text(100);
        $this->title2 = $faker->company();
        $this->uuid2 = $faker->uuid();
        $this->description2 = $faker->text(100);
        $this->validHoldingData = [
            'uuid' => $this->uuid1,
            'title' => $this->title1,
            'description' => $this->description1
        ];

        $this->createdHoldingIds->push(Holding::create($this->validHoldingData)->id);
    }

    private function markForRemoval($response)
    {
        $this->createdHoldingIds->push(json_decode($response->getContent())->id);
    }

    public function tearDown() : void
    {
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
        $response = $this->get(route('planning.holdings.index'));
        $response->assertStatus(200);
    }

    public function test_when_requesting_a_list_of_holdings_it_returns_a_valid_json_array()
    {
        $response = $this->get(route('planning.holdings.index'));
        $response->assertJson([]);
    }

    public function test_it_can_create_a_holding()
    {
        $response = $this->json('POST',
            route('planning.holdings.store'),
            ['title' => $this->title2, 'description' => $this->description2]);
        $response->assertJson(['title' => $this->title2, 'description' => $this->description2]);
        $this->markForRemoval($response);
    }
 
}
