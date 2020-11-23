<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Interfaces\KeycloakClientInterface;
use App\Services\KeycloakClientService;
use App\User;

class FakeKeycloakClient
{
    private $users;
    private $fakeResponse;
    private $fakeResponseFunction;
    private $lastAssignedId;

    public function __construct(Collection $users)
    {
        $this->users = $users;
        $this->fakeResponse = null;
        $this->fakeResponseFunction = null;
        $this->lastAssignedId = '';
    }

    public function setFakeResponse($function, $response)
    {
        $this->fakeResponse = $response;
        $this->fakeResponseFunction = $function;
    }

    public function lastAssignedId()
    {
        return $this->lastAssignedId;
    }

    public function getUsers()
    {
        if ($this->fakeResponseFunction == __FUNCTION__) {
            return $this->fakeResponse;
        }

        return $this->users->map( function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'password' => $user->password,
                'first_name' => $user->full_name,
                'last_name' => $user->full_name,
                'email' => $user->email,
                'account_uuid' => $user->account_uuid ];
        })->toArray();
    }

    public function createUser(array $userInfo)
    {
        if ($this->fakeResponseFunction == __FUNCTION__) {
            return $this->fakeResponse;
        }

        if (!is_array($userInfo)) {
            return ['errorMessage' => 'Invalid input'];
        }

        if (!isset($userInfo['id']) ||
            !isset($userInfo['username']) ||
            strlen($userInfo['id']) == 0 ||
            strlen($userInfo['username']) == 0 ||
            count($userInfo) > 7) {
            return ['errorMessage' => 'Invalid input'];
        }

        foreach ($this->users as $user) {
            if ($user->username == $userInfo['username']) {
                $this->lastAssignedId = Str::uuid();
                $user->setIdAttribute($this->lastAssignedId);
                break;
            }
        }

        // Missing username is not valid
        if (!isset($userInfo['username']) || strlen($userInfo['username']) == 0) {
            return ['errorMessage' => 'Username is not set'];
        }

        return ['content' => ''];
    }

    public function updateUser(array $userInfo)
    {
        if ($this->fakeResponseFunction == __FUNCTION__) {
            return $this->fakeResponse;
        }

        if (!is_array($userInfo)) {
            return ['errorMessage' => 'Invalid input'];
        }

        if (!isset($userInfo['id']) ||
            !isset($userInfo['username']) ||
            strlen($userInfo['id']) == 0 ||
            strlen($userInfo['username']) == 0 ||
            count($userInfo) > 6) {
            return ['errorMessage' => 'Invalid input'];
        }

        foreach ($this->users as $user) {
            if ($user->username == $userInfo['username'] || $user->id == $userInfo['id']) {
                $user->id = $userInfo['id'];
                $user->username = $userInfo['username'];
                $user->email = $userInfo['email'];
                return ['content' => ''];
            }
        }

        return ['errorMessage' => 'Could not find user'];
    }

    public function deleteUser(array $userInfo)
    {
        if ($this->fakeResponseFunction == __FUNCTION__) {
            return $this->fakeResponse;
        }

        if (!is_array($userInfo)) {
            return ['errorMessage' => 'Invalid input'];
        }

        if (!isset($userInfo['id']) ||
            strlen($userInfo['id']) == 0 ||
            count($userInfo) != 1) {
            return ['errorMessage' => 'Invalid input'];
        }

        foreach ($this->users as $user) {
            if ($user->id == $userInfo['id']) {
                $this->users->forget($userInfo['id']);
                return ['content' => ''];
            }
        }

        return ['errorMessage' => 'Could not find user'];
    }
}

class KeycloakClientTest extends TestCase
{
    private $users;
    private $service;
    private $client;

    protected function setUp() : void
    {
        parent::setUp();

        // Create test users
        $this->users = collect([
            new User([
                'username' => 'kctest1',
                'password' => 'kctestpass1',
                'full_name' => 'Key Cloaksson',
                'email' => 'kc1@piql.com',
                'account_uuid' => '1d860602-130e-4624-99ad-d532e1b662ab']),
            new User([
                'username' => 'kctest2',
                'password' => 'kctestpass2',
                'full_name' => 'Key Cloaksson Sr.',
                'email' => 'kc2@piql.com',
                'account_uuid' => '98dd00df-afba-4031-8dd1-18c09a29acbd']),
            new User([
                'username' => 'kctest3',
                'password' => 'kctestpass3',
                'full_name' => 'Key Cloaksson Jr.',
                'email' => 'kc3@piql.com',
                'account_uuid' => '46e9d918-270b-43fe-b3a8-4e919e28a05c']) ]);
        foreach ($this->users as $user) {
            $user->setIdAttribute(Str::uuid());
        }

        // Create service with fake client
        $this->client = new FakeKeycloakClient($this->users);
        // $this->service = $this->app->makeWith('App\Services\KeycloakClientService', ['keycloakClient' => $this->client]);
        $this->service = new KeycloakClientService(['client'=>&$this->client]);
    }

    public function test_when_injecting_an_instance_of_KeycloakClientInterface_it_makes_a_KeycloakClientService()
    {
        $service = $this->app->make( KeycloakClientInterface::class );
        $this->assertInstanceOf( KeycloakClientService::class, $service );
    }

    public function test_when_requesting_users_a_user_list_is_returned()
    {
        $users = $this->service->getUsers();
        $this->assertEquals($users, $this->client->getUsers());
        $this->assertEquals(count($users), $this->users->count());
    }

    public function test_when_requesting_users_and_response_is_null_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('getUsers', null);

        $users = $this->service->getUsers();
    }

    public function test_when_requesting_users_and_response_is_string_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('getUsers', 'something');
        $users = $this->service->getUsers();
    }

    public function test_when_requesting_users_and_response_is_flat_array_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('getUsers', ['id' => $this->users[0]->id, 'username' => $this->users[0]->username]);
        $users = $this->service->getUsers();
    }

    public function test_when_requesting_users_and_response_is_missing_id_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('getUsers', [['username' => $this->users[0]->username], ['username' => $this->users[1]->username]]);
        $users = $this->service->getUsers();
        var_dump($users);
    }

    public function test_when_creating_a_user_a_user_is_returned()
    {
        $user = $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_when_creating_a_user_a_new_id_is_generated()
    {
        $user = $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);
        $this->assertEquals($user->id, $this->client->lastAssignedId());
        $this->assertNotEquals($user->id, $this->users[0]);
    }

    public function test_when_creating_a_user_without_username_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $user = $this->users[0];
        $user->username = '';
        $this->service->createUser($user->account_uuid, $user);
    }

    public function test_when_creating_a_user_and_response_is_null_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('createUser', null);
        $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);
    }

    public function test_when_creating_a_user_and_response_is_misformed_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('createUser', ['convenient' => '']);
        $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);
    }

    public function test_when_updating_a_user_the_user_is_updated()
    {
        $user = $this->service->editUser($this->users[0]->account_uuid, $this->users[0]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user, $this->users[0]);
    }

    public function test_when_updating_missing_user_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $user = $this->users[0];
        $user->username = '';
        $this->service->editUser($user->account_uuid, $user);
    }

    public function test_when_updating_a_user_and_response_is_null_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('updateUser', null);
        $this->service->editUser($this->users[0]->account_uuid, $this->users[0]);
    }

    public function test_when_updating_a_user_and_response_is_misformed_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('updateUser', ['convenient' => '']);
        $this->service->editUser($this->users[0]->account_uuid, $this->users[0]);
    }

    public function test_when_deleting_a_user_the_user_is_deleted()
    {
        $this->assertNull($this->service->deleteUser($this->users[0]->id));
    }

    public function test_when_deleting_missing_user_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->service->deleteUser('made-up-id');
    }

    public function test_when_deleting_a_user_and_response_is_null_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('deleteUser', null);
        $this->service->deleteUser($this->users[0]->account_uuid, $this->users[0]);
    }

    public function test_when_deleting_a_user_and_response_is_misformed_exception_is_thrown()
    {
        $this->expectException(\Exception::class);
        $this->client->setFakeResponse('deleteUser', ['convenient' => '']);
        $this->service->deleteUser($this->users[0]->account_uuid, $this->users[0]);
    }

    public function test_when_requesting_user_by_id_a_user__matching_id_is_returned()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function ( $mock ) {
                $mock->shouldReceive( 'getUserById' )->times(1);
            } )
        );

        $service = $this->app->make( KeycloakClientInterface::class );

        $users = $service->getUserById("user-uuid");
    }

    public function test_when_searchilg_organisation_users_only_user_with_org_id_are_returned()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function ( $mock ) {
                $mock->shouldReceive( 'searchOrganizationUsers' )->times(1);
            } )
        );

        $service = $this->app->make( KeycloakClientInterface::class );

        $users = $service->searchOrganizationUsers("org-uuid");
    }

    public function test_a_user_can_change_password()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function($mock) {
                $mock->shouldReceive('changePassword')->times(1);
            } )
        );
        $service = $this->app->make( KeycloakClientInterface::class );
        $users = $service->changePassword('id', 'password');
    }

    public function test_can_globally_logout_user()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function($mock) {
                $mock->shouldReceive('logoutUser')->times(1);
            } )
        );
        $service = $this->app->make( KeycloakClientInterface::class );
        $users = $service->logoutUser('id');
    }

    public function test_can_block_user_from_logging_in()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function($mock) {
                $mock->shouldReceive('blockUser')->times(1);
            } )
        );
        $service = $this->app->make( KeycloakClientInterface::class );
        $users = $service->blockUser('id');
    }

    public function test_can_ubblock_user_to_enable_login()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function($mock) {
                $mock->shouldReceive('unblockUser')->times(1);
            } )
        );
        $service = $this->app->make( KeycloakClientInterface::class );
        $users = $service->unblockUser('id');
    }
}
