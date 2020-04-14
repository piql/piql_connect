<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class UserStatsApiTest extends TestCase
{
	use DatabaseTransactions;

	private $expectedStatsFormat;

	public function setUp( ) : void
	{
		parent::setUp();
		$this->testUser = factory( \App\User::class )->create();
		Passport::actingAs( $this->testUser );

		$this->expectedStatsFormat = [
			"data" => [
				"onlineDataIngested",
				"offlineDataIngested",
				"onlineAIPsIngested",
				"offlineAIPsIngested",
				"offlineReelsCount",
				"offlinePagesCount",
				"AIPsRetrievedCount",
				"DataRetrieved"
			]
		];

	}


	/**
	 * An authenticated user can request stats
	 *
	 * @return void
	 */
	public function test_when_requesting_current_user_stats_it_responds_ok()
	{
		$response = $this->get('/api/v1/stats/user/current');

		$response->assertStatus(200);
	}

	/**
	 * The response is well formatted
	 *
	 * @return void
	 */
	public function test_when_requesting_current_user_stats_it_is_well_formatted()
	{
		$response = $this->get('/api/v1/stats/user/current');
		$response->assertJsonStructure( $this->expectedStatsFormat );
	}



}
