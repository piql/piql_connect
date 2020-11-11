<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Interfaces\KeycloakClientInterface;
use App\Services\KeycloakClientService;
use App\User;

class FakeKeycloakClient
{
    private $users;
    private $fakeResponse;
    private $fakeResponseSet;
    private $lastAssignedId;

    public function __construct(Collection $users)
    {
        $this->users = $users;
        $this->fakeResponse = null;
        $this->fakeResponseSet = false;
        $this->lastAssignedId = '';
    }

    public function setFakeResponse($response)
    {
        $this->fakeResponse = $response;
        $this->fakeResponseSet = true;
    }

    public function lastAssignedId()
    {
        return $this->lastAssignedId;
    }

    public function getUsers()
    {
        if ($this->fakeResponseSet) {
            return $this->fakeResponse;
        }

        return $this->users->map( function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'password' => $user->password,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'account_uuid' => $user->account_uuid ];
        })->toArray();
    }

    public function createUser(array $userInfo)
    {
        foreach ($this->users as $user) {
            if ($user->username == $userInfo['username']) {
                $this->lastAssignedId = Str::uuid();
                $user->setIdAttribute($this->lastAssignedId);
                break;
            }
        }
        return ['content' => ''];
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
        $this->service = $this->app->makeWith('App\Services\KeycloakClientService', ['app' => $this->app, 'keycloakClient' => $this->client]);
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

    public function test_when_requesting_users_and_response_is_misformed_exception_is_thrown()
    {
        $this->expectException(\Exception::class);

        // Null response is not valid
        $this->client->setFakeResponse(null);
        $users = $this->service->getUsers();

        // Response cannot be string
        $this->client->setFakeResponse('something');
        $users = $this->service->getUsers();

        // Array is flattened, expecting multi-dimensional array
        $this->client->setFakeResponse(['id' => $users[0]->id, 'username' => $users[0]->username]);
        $users = $this->service->getUsers();

        // User id is a required attribute
        $this->client->setFakeResponse([['username' => $users[0]->username], ['username' => $users[1]->username]]);
        $users = $this->service->getUsers();
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

    public function test_when_creating_a_user_and_response_is_misformed_exception_is_thrown()
    {
        $this->expectException(\Exception::class);

        // Null response is not valid
        $this->client->setFakeResponse(null);
        $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);

        // Response cannot be string
        $this->client->setFakeResponse('something');
        $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);

        // Empty array is not valid response
        $this->client->setFakeResponse([]);
        $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);

        // Array with random data is not valid response
        $this->client->setFakeResponse(['convenient']);
        $this->service->createUser($this->users[0]->account_uuid, $this->users[0]);

        // TODO: Decide which function we're setting faked response for. createUser is calling getUsers
    }



/*
    public function test_when_updating_a_user_the_user_is_updated()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function ( $mock ) {
                $mock->shouldReceive( 'editUser' )->times(1);
            } )
        );

        $service = $this->app->make( KeycloakClientInterface::class );

        $service->editUser(strval($this->user->id), $this->user);
    }

    public function test_when_deleting_a_user_the_user_is_deleted()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function ( $mock ) {
                $mock->shouldReceive( 'deleteUser' )->times(1);
            } )
        );

        $service = $this->app->make( KeycloakClientInterface::class );

        $service->deleteUser(strval($this->user->id));
    }
*/
}
