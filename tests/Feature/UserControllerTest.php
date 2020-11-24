<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;
use Mockery;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs($this->user);
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
        $response = $this->get('/api/v1/admin/users/'.$u->id);
        $response->assertOk();
        $user = $response->decodeResponseJson('data');
        $this->assertEquals($u->id, $user['id']);

    }

    public function test_fetching_non_existing_user_by_id_returns_404()
    {
        $response = $this->get('/api/v1/admin/users/'.Str::uuid());
        $response->assertNotFound();
        $response->assertHeader('Content-Type', 'application/json');
        $message = $response->decodeResponseJson('message');
        $this->assertContains('Not Found', $message);
    }

    public function test_disabling_user_sets_disabledOn_field()
    {
        $response = $this->json('post','/api/v1/admin/users/disable', [
            'users' => [factory(User::class)->create()->id]
        ]);
        $response->assertOk();
        $id = $response->decodeResponseJson('users')[0]['id'];
        $u = User::find($id);
        $this->assertNotNull($u->disabled_on);
    }

    public function test_enabling_user_sets_disabledOn_field_to_null()
    {
        $response = $this->json('post','/api/v1/admin/users/disable', [
            'users' => [factory(User::class)->create()->id]
        ]);
        $response->assertOk();
        $id = $response->decodeResponseJson('users')[0]['id'];
        $response = $this->json('post','/api/v1/admin/users/enable', [
            'users' => [$id]
        ]);
        $u = User::find($id);
        $this->assertNull($u->disabled_on);
    }

    public function test_updating_user_returns_200()
    {
        $keycloakClient = Mockery::mock('App\Interfaces\KeycloakClientInterface');
        $keycloakClient->expects('editUser')
                       ->once();

        $this->app->bind('App\Interfaces\KeycloakClientInterface',
            function( $app ) use($keycloakClient) {
                return $keycloakClient;
            }
        );

        $newEmail = $this->user->email . '_TEST';
        $newFullName = $this->user->full_name . '_TEST';
        $response = $this->json('put','/api/v1/admin/users/' . $this->user->id, [
            'email' => $newEmail,
            'full_name' => $newFullName
        ]);

        $response->assertStatus(200);
        $responseData = $response->decodeResponseJson('data');
        $this->assertEquals($this->user->id, $responseData['id']);
        $this->assertEquals($this->user->username, $responseData['username']);
        $this->assertEquals($newEmail, $responseData['email']);
        $this->assertEquals($newFullName, $responseData['full_name']);
    }

    public function test_updating_missing_user_returns_404()
    {
        $response = $this->json('put','/api/v1/admin/users/non-existing-id', [
            'email' => $this->user->email,
            'full_name' => $this->user->full_name
        ]);

        $response->assertStatus(404);
    }

    public function test_updating_with_duplicate_email_returns_400()
    {
        $existingUser = factory(User::class)->create();

        $response = $this->json('put','/api/v1/admin/users/' . $this->user->id, [
            'email' => $existingUser->email,
            'full_name' => $this->user->full_name
        ]);

        $response->assertStatus(400);
    }
}
