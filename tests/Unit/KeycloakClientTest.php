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
}
