<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use App\Holding;
use App\Collection;

class HoldingApiTest extends TestCase
{
    use DatabaseTransactions;

    private $collection;
    private $rndTitle1;
    private $rndTitle2;
    private $rndTitle3;
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $faker = Faker::create();
        $this->collection = Collection::create(['title' => 'HoldingApiTestCollection'])->fresh();
        $this->rndTitle1 = $faker->slug(1);
        $this->rndTitle2 = $faker->slug(1);
        $this->rndTitle3 = $faker->slug(1);

        Holding::create(['title' => $faker->slug(3), 'description' => $faker->text(20), 'collection_uuid' => $this->collection->uuid]);
    }

    public function test_can_get_list_of_holdings()
    {

        $response = $this->get( route( 'api.metadata.collections.holdings.index', $this->collection ) );
        $response->assertStatus( 200 );
    }

    public function test_can_get_list_of_holdings_for_a_given_collection()
    {
        $second_collection = Collection::create(['title' => 'HoldingApiTestOtherCollection']);

        $testHoldingBase = [
            'collection_uuid' => $second_collection->uuid
        ];

        $f1 = Holding::create( ['title' => $this->rndTitle1] + $testHoldingBase );
        $f2 = Holding::create( ['title' => $this->rndTitle2] + $testHoldingBase );
        $f3 = Holding::create( ['title' => $this->rndTitle3] + $testHoldingBase );

        $response = $this->get( route( 'api.metadata.collections.holdings.index', [$second_collection] ) );
        $response
            ->assertStatus(200)
            ->assertJson(
            ['data' => [
                [ 'id' => $f1->id, 'title'=> $this->rndTitle1],
                [ 'id' => $f2->id, 'title'=> $this->rndTitle2],
                [ 'id' => $f3->id, 'title'=> $this->rndTitle3]
            ] ]);
    }


    public function test_when_creating_given_data_it_should_respond_with_403()
    {
        $response = $this->post( route( 'api.metadata.collections.holdings.store', [$this->collection] ), [] );
        $response->assertStatus(403);
    }

}
