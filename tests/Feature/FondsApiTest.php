<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as faker;
use App\Fonds;
use App\Holding;

class FondsApiTest extends TestCase
{
    private $testTitle1;
    private $owner_holding;
    private $testFondsData;
    private $createdFondsId;
    private $holdingCount;

    public function setUp() : void
    {
        parent::setUp();
        $this->holdingCount = Holding::count();
        $faker = Faker::create();
        $this->testTitle1 = $faker->slug(1);
        $this->owner_holding = Holding::create(['title' => 'FondsApiTestHolding'])->fresh();
        $this->testFondsData = [
            'title' => $this->testTitle1,
            'owner_holding_uuid' => $this->owner_holding->uuid
        ];
    }

    private function markFondsForRemoval($response)
    {
        $this->createdFondsId = $response->getData()->data->id;
    }

    public function tearDown() : void
    {
        $this->owner_holding->delete();
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
