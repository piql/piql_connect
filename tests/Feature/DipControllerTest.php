<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;

class DipControllerTest extends TestCase
{	
	public function setUp() : void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
		Passport::actingAs($this->user);
	}
	
	public function test_show_file_returns_200()
    {
        $response = $this->get('/api/v1/access/dips/files/123');
        $response->assertOk();
    }
}
