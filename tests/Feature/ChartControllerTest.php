<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;

class ChartControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;

    public function setUp() : void
    {
        parent::setUp();

        $this->testUser = factory(\App\User::class)->create();
        Passport::actingAs($this->testUser);
    }

    public function test_when_requesting_monthly_ingested_aips_it_responds_200()
    {
        $response = $this->get( route( 'monthlyIngestedAIPs' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_monthly_ingested_data_it_responds_200()
    {
        $response = $this->get( route( 'monthlyIngestedData' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_monthly_accessed_aips_it_responds_200()
    {
        $response = $this->get( route( 'monthlyAccessedAIPs' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_monthly_accessed_data_it_responds_200()
    {
        $response = $this->get( route( 'monthlyAccessedData' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_aips_ingested_it_responds_200()
    {
        static::markTestSkipped('Daily statistics will be enabled after the 1.0 release (ref CON-748)');

        $response = $this->get( route( 'dailyOnlineAIPsIngested' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_data_ingested_it_responds_200()
    {
        static::markTestSkipped('Daily statistics will be enabled after the 1.0 release (ref CON-748)');

        $response = $this->get( route( 'dailyOnlineDataIngested' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_aips_accessed_it_responds_200()
    {
        static::markTestSkipped('Daily statistics will be enabled after the 1.0 release (ref CON-748)');

        $response = $this->get( route( 'dailyOnlineAIPsAccessed' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_online_data_accessed_it_responds_200()
    {
        static::markTestSkipped('Daily statistics will be enabled after the 1.0 release (ref CON-748)');

        $response = $this->get( route( 'dailyOnlineDataAccessed' ) );
        $response->assertStatus(200);
    }

    public function test_when_requesting_daily_fileformats_ingested_it_responds_200()
    {
        static::markTestSkipped('Daily statistics will be enabled after the 1.0 release (ref CON-748)');

        $response = $this->get( route( 'fileFormatsIngested' ) );
        $response->assertStatus(200);
    }

}
