<?php

namespace Tests\Feature;

use App\Services\UserRegistrationService;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;

class UserRegistrationControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs($this->user);
    }

    public function test_initiating_user_registration_returns_201() {
        $response = $this->postJson('/api/v1/registration/register', [
            'name'=>'Mark Twain', 'email'=>'mt20@authors.org', 'username'=>'mtwen'
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['message']);
    }

    public function test_initiating_duplicate_user_registration_returns_400() {
        UserRegistrationService::registerUser('Maek Twein', 'mkwen', 'mt21@authors.org', '');
        $response = $this->postJson('/api/v1/registration/register', [
            'name'=>'Maek Twein', 'email'=>'mt21@authors.org', 'username'=>'mkwen'
        ]);
        $response->assertStatus(400);
        $this->assertEquals("Validation Failed", $response->decodeResponseJson('message'));
        $response->assertJsonStructure(['message', 'errors'=>['username', 'email']]);
    }
}
