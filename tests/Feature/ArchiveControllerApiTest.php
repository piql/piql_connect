<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use App\Holding;
use App\Archive;

class HoldingApiTest extends TestCase
{
    use DatabaseTransactions;

    private $testTitle1;
    private $owner_archive;
    private $testHoldingData;
    private $createdHoldingId;
    private $archiveCount;
    private $rndTitle1;
    private $rndTitle2;
    private $rndTitle3;
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $this->archiveCount = Archive::count();
        $faker = Faker::create();
        $this->testTitle1 = $faker->slug(1);
        $this->owner_archive = Archive::create(['title' => 'HoldingApiTestArchive'])->fresh();
        $this->testHoldingData = [
            'title' => $this->testTitle1,
            'owner_archive_uuid' => $this->owner_archive->uuid
        ];
        $this->rndTitle1 = $faker->slug(1);
        $this->rndTitle2 = $faker->slug(1);
        $this->rndTitle3 = $faker->slug(1);

        Holding::create(['title' => $faker->slug(3), 'description' => $faker->text(20), 'owner_archive_uuid' => $this->owner_archive->uuid]);
    }

    public function test_can_get_list_of_holdings()
    {

        $response = $this->get( route( 'api.metadata.archives.holdings.index', $this->owner_archive ) );
        $response->assertStatus( 200 );
    }

    public function test_can_get_list_of_holdings_for_a_given_archive()
    {
        $second_archive = Archive::create(['title' => 'ArchiveApiTestOtherArchive']);

        $testHoldingBase = [
            'owner_archive_uuid' => $second_archive->uuid
        ];

        $f1 = Holding::create( ['title' => $this->rndTitle1] + $testHoldingBase );
        $f2 = Holding::create( ['title' => $this->rndTitle2] + $testHoldingBase );
        $f3 = Holding::create( ['title' => $this->rndTitle3] + $testHoldingBase );
        
        $response = $this->get( route( 'api.metadata.archives.holdings.index', [$second_archive] ) );
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
        $response = $this->post( route( 'api.metadata.archives.holdings.store', [$this->owner_archive] ), [] );
        $response->assertStatus(403);
    }

}
