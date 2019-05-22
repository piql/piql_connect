<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
Use Traits\UrlFrom;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    protected $testUser = null;
    protected $bad_credentials = [];

    protected function setUp() : void
    {
        parent::setUp();

        $this->testUser = factory(\App\User::class)->create();

        $bad_credentials = [
            'username' => 'doesnotexist',
            'password' => 'notworking'
        ];
    }

    protected function tearDown() : void
    {
        $this->testUser->delete();

        parent::tearDown();
    }
 
    public function test_when_get_requested_it_responds_with_200_Ok()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_given_valid_credentials_when_posted_it_logs_the_user_in()
    {
        $username = $this->testUser['username'];
        $password = $this->testUser['password'];
        $valid_credentials = ['username' => $username, 'password' => "secret"];
        $response = $this->from('/login')
                         ->post('/login', $valid_credentials);

        $response->assertRedirect('/');

    }

    public function test_given_bad_credentials_when_posted_it_responds_with_404_Not_Found()
    {
        $response = $this->from('/login')
                         ->post('/login', $this->bad_credentials);

        $response->assertRedirect('/login');
    }
	
}
