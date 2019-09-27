<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\User;

class LoginTest extends TestCase
{
    protected $testUser;
    protected $bad_username;
    protected $bad_password;
    protected $valid_credentials;

    protected function setUp() : void
    {
        parent::setUp();

        $testPassword = "testPassword";
        $this->testUser = factory(\App\User::class)->create([ 'password' => Hash::make( $testPassword ) ]);

        $this->valid_credentials = [
            'username' => $this->testUser->username,
            'password' => $testPassword
        ];

        $this->bad_password = [
            'username' => $this->testUser->username,
            'password' => 'nottherightpassword'
        ];

        $this->bad_username = [
            'username' => 'thisisnotmyusername',
            'password' => $testPassword
        ];
    }

    protected function tearDown() : void
    {
        $this->testUser->delete();

        parent::tearDown();
    }
 
    public function test_when_get_requested_it_responds_with_200_Ok()
    {
        $response = $this->get( '/login' );

        $response->assertStatus( 200 );
    }

    public function test_given_a_username_does_not_exist_when_posted_to_login_it_responds_with_a_redirect_to_login()
    {
        $response = $this->from( '/login' )
                         ->post( '/login', $this->bad_username );

        $response->assertRedirect( '/login' );
    }

    public function test_given_a_valid_username_with_the_wrong_password_when_posted_to_login_it_responds_with_a_redirect_to_login()
    {
        $response = $this->from( '/login' )
                         ->post( '/login', $this->bad_password );

        $response->assertRedirect('/login' );
    }


    public function test_given_valid_credentials_when_posted_to_login_it_responds_with_a_redirect_to_root()
    {
        $response = $this->from( '/login' )
                         ->post( '/login', $this->valid_credentials );

        $response->assertRedirect( '/' );
    }

    public function test_given_valid_credentials_when_posted_to_login_it_authenticates_the_correct_user()
    {
        $response = $this->from( '/login' )
                         ->post( '/login', $this->valid_credentials );

        $this->assertAuthenticatedAs( $this->testUser ) ;
    }

    public function test_given_we_are_authenticated_when_posting_to_logout_it_logs_the_user_out()
    {
        $this->be( $this->testUser );

        $response = $this->from( '/' )->get( '/logout');

        $this->assertGuest();
    }
	
}
