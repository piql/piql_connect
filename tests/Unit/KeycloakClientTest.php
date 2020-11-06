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
}
