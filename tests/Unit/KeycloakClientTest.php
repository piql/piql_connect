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

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function getUsers()
    {
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

    public function test_when_adding_a_user_the_user_is_added()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function ( $mock ) {
                $mock->shouldReceive( 'addUser' )->times(1);
            } )
        );

        $service = $this->app->make( KeycloakClientInterface::class );

        $service->addUser($this->user->account_uuid, $this->user);
    }

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
}
