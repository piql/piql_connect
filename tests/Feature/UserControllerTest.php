<?php

namespace Tests\Feature;

use App\User;
use Exception;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        if(!$this->token = JWTAuth::attempt([
            'username'=>$this->user->username,
            'password'=>'secret',
        ])) throw new Exception("Failed to authenticate user");
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ]);
        // Passport::actingAs($this->user);
    }

    public function test_listing_users_returns_200()
    {
        $response = $this->get('/api/v1/admin/users');
        $response->assertOk();
    }

    public function test_fetching_existing_user_by_id_returns_200()
    {
        $u = factory(User::class)->create();
        $this->assertNotNull($u);
        $response = $this->get('/api/v1/admin/users/' . $u->getIdAttribute());
        $response->assertOk();
        $user = $response->decodeResponseJson('data');
        $this->assertEquals($u->id, $user['id']);
    }

    public function test_fetching_non_existing_user_by_id_returns_404()
    {
        $response = $this->get('/api/v1/admin/users/' . Str::uuid());
        $response->assertNotFound();
        $response->assertHeader('Content-Type', 'application/json');
        $message = $response->decodeResponseJson('message');
        $this->assertContains('Not Found', $message);
    }

    public function test_disabling_user_sets_disabledOn_field()
    {
        $response = $this->json('post', '/api/v1/admin/users/disable', [
            'users' => [factory(User::class)->create()->getIdAttribute()]
        ]);
        $response->assertOk();
        $id = $response->decodeResponseJson('users')[0]['id'];
        $u = User::find($id);
        $this->assertNotNull($u->disabled_on);
    }

    public function test_enabling_user_sets_disabledOn_field_to_null()
    {
        $response = $this->json('post', '/api/v1/admin/users/disable', [
            'users' => [factory(User::class)->create()->getIdAttribute()]
        ]);
        $response->assertOk();
        $id = $response->decodeResponseJson('users')[0]['id'];
        $response = $this->json('post', '/api/v1/admin/users/enable', [
            'users' => [$id]
        ]);
        $u = User::find($id);
        $this->assertNull($u->disabled_on);
    }
}
