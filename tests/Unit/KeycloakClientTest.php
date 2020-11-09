<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Interfaces\KeycloakClientInterface;
use App\Services\KeycloakClientService;
use App\User;

class KeycloakClientTest extends TestCase
{
    private $user;

    protected function setUp() : void
    {
        parent::setUp();

        $this->user = new User([
            'username' => 'kctest',
            'password' => 'kctestpass',
            'full_name' => 'Key Cloaksson',
            'email' => 'kc@piql.com',
            'account_uuid' => '1d860602-130e-4624-99ad-d532e1b662ab'
        ]);
    }

    public function test_when_injecting_an_instance_of_KeycloakClientInterface_it_makes_a_KeycloakClientService()
    {
        $service = $this->app->make( KeycloakClientInterface::class );
        $this->assertInstanceOf( KeycloakClientService::class, $service );
    }

    public function test_when_requesting_users_a_user_list_is_returned()
    {
        $this->instance( KeycloakClientInterface::class, Mockery::mock(
            KeycloakClientService::class, function ( $mock ) {
                $mock->shouldReceive( 'getUsers' )->times(1);
            } )
        );

        $service = $this->app->make( KeycloakClientInterface::class );

        $users = $service->getUsers();
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
