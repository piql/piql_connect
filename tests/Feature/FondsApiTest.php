<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use App\Fonds;
use App\Holding;

class FondsApiTest extends TestCase
{
    private $testTitle1;
    private $owner_holding;
    private $testFondsData;
    private $createdFondsId;
    private $holdingCount;
    private $rndTitle1;
    private $rndTitle2;
    private $rndTitle3;
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $this->holdingCount = Holding::count();
        $faker = Faker::create();
        $this->testTitle1 = $faker->slug(1);
        $this->owner_holding = Holding::create(['title' => 'FondsApiTestHolding'])->fresh();
        $this->testFondsData = [
            'title' => $this->testTitle1,
            'owner_holding_uuid' => $this->owner_holding->uuid
        ];
        $this->rndTitle1 = $faker->slug(1);
        $this->rndTitle2 = $faker->slug(1);
        $this->rndTitle3 = $faker->slug(1);
    }

    private function markFondsForRemoval($response)
    {
        $this->createdFondsId = $response->getData()->data->id;
    }

    public function tearDown() : void
    {
        $this->owner_holding->delete();
        $this->testUser->delete();

        if($this->createdFondsId){
            $fonds = Fonds::find($this->createdFondsId);
            if($fonds){
                $fonds->delete();
            }
        }
        $this->assertEquals($this->holdingCount, Holding::count());
        parent::tearDown();
    }

    public function test_can_get_list_of_fonds()
    {
        $response = $this->get(route('planning.fonds.index'));

        $response->assertStatus(200);
    }

    public function test_can_get_list_of_fonds_for_a_given_holding()
    {
        $second_holding = Holding::create(['title' => 'FondsApiTestOtherHolding']);

        $testFondsBase = [
            'owner_holding_uuid' => $second_holding->uuid
        ];

        $f1 = Fonds::create(['title' => $this->rndTitle1]+$testFondsBase);
        $f2 = Fonds::create(['title' => $this->rndTitle2]+$testFondsBase);
        $f3 = Fonds::create(['title' => $this->rndTitle3]+$testFondsBase);
        
        $response = $this->get('/api/v1/planning/holdings/'.$second_holding->uuid.'/fonds');
        $response->assertJson(
            ['data' => [
                [ 'id' => $f1->id, 'title'=> $this->rndTitle1], 
                [ 'id' => $f2->id, 'title'=> $this->rndTitle2], 
                [ 'id' => $f3->id, 'title'=> $this->rndTitle3] 
            ] ]);

        $response->assertStatus(200);
        $second_holding->delete();
        $f1->delete();
        $f2->delete();
        $f3->delete();
    }


    public function test_when_creating_given_valid_data_was_posted_it_should_respond_with_201()
    {
        $response = $this->post(route('planning.fonds.store'), $this->testFondsData);
        $response->assertStatus(201);
        $this->markFondsForRemoval($response);
    }

    public function test_when_creating_given_a_valids_fonds_was_posted_the_response_contains_a_valid_id()
    {
        $response = $this->post(route('planning.fonds.store'), $this->testFondsData);
        $result = $response->getData();
        $model = Fonds::find($result->data->id);
        $this->assertTrue($model->title == $this->testTitle1);
        $this->markFondsForRemoval($response);
    }

    public function test_when_creating_given_a_bad_title_it_responds_with_422()
    {
        $response = $this->post(route('planning.fonds.store'), ['title' => '' ]+$this->testFondsData);
        $response->assertStatus(422);
        $content = $response->getContent();
        $this->assertContains('title', $content);
    }

    public function test_when_creating_given_a_bad_owner_holding_it_responds_with_422()
    {
        $response = $this->post(route('planning.fonds.store'), ['owner_holding_uuid' => '123' ]+$this->testFondsData);
        $response->assertStatus(422);
        $content = $response->getContent();
        $this->assertContains('owner_holding', $content);
    }


}
