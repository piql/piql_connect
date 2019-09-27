<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class DashboardControllerTest extends TestCase
{
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = \App\User::create([
            'username' => 'BagApiTestUser', 
            'password' => 'notinuse',
            'full_name' => 'BagApi TestUser',
            'email' => 'bagapitestuser@localhost'
        ]);

        Passport::actingAs($this->testUser);
    }

    public function tearDown() : void
    {
        $this->testUser->delete();
        parent::tearDown();
    }

    public function test_when_requesting_monthly_online_aips_ingested_it_responds_200()
    {
        $response = $this->get( route( 'monthlyOnlineAIPsIngested' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_monthly_online_data_ingested_it_responds_200()
    {
        $response = $this->get( route( 'monthlyOnlineDataIngested' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_monthly_online_aips_accessed_it_responds_200()
    {
        $response = $this->get( route( 'monthlyOnlineAIPsAccessed' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_monthly_online_data_accessed_it_responds_200()
    {
        $response = $this->get( route( 'monthlyOnlineDataAccessed' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_aips_ingested_it_responds_200()
    {
        $response = $this->get( route( 'dailyOnlineAIPsIngested' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_data_ingested_it_responds_200()
    {
        $response = $this->get( route( 'dailyOnlineDataIngested' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_aips_accessed_it_responds_200()
    {
        $response = $this->get( route( 'dailyOnlineAIPsAccessed' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_data_accessed_it_responds_200()
    {
        $response = $this->get( route( 'dailyOnlineDataAccessed' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_fileformats_ingested_it_responds_200()
    {
        $response = $this->get( route( 'fileFormatsIngested' ) );
        $response->assertStatus(200);
    }

}
