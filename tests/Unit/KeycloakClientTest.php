<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Interfaces\KeycloakClientInterface;
use App\Services\KeycloakClientService;

class KeycloakClientTest extends TestCase
{
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
