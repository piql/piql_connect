<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Interfaces\KeycloakClientInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use App\Organization;
use App\User;

class ArtisanCommandOrganizationAddUserTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected function setUp() : void
    {
        parent::setUp();
        app()->bind(KeycloakClientInterface::class,
            function($app)  {
                return \Mockery::mock(KeycloakClientInterface::class, function($mock) {
                    $mock->shouldReceive("createUser")->andReturn(
                        \Mockery::mock(User::class)
                    );
                });
            }
        );
    }

    /**
     * @test add a user to an organization
     */
    public function test_create_a_basic_organization() {
        $users = User::count();
        $organization = Organization::create([
            "name" => $this->faker->company
        ]);
        $this->artisan("organization:adduser",[
            "organizationId" => $organization->uuid,
            "firstName" => $this->faker->name,
            "lastName" => $this->faker->lastName,
            "email" => $this->faker->email,
            "username" => $this->faker->userName,
            "--yes" => true,
        ])->assertExitCode(0);

        $this->assertEquals($users+1, User::count());
    }

    /**
     * @test add a user to an organization with without passing parameters
     */
    public function test_add_a_user_to_an_organization_without_passing_parameters() {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
        $this->artisan("organization:adduser")->assertExitCode(0);
    }

    /**
     * @test add a user to an organization with empty organization uuid
     */
    public function test_add_user_to_an_organization_with_empty_organization_uuid() {
        $this->artisan("organization:adduser",[
            "organizationId" => "",
            "firstName" => $this->faker->name,
            "lastName" => $this->faker->lastName,
            "email" => $this->faker->email,
            "username" => $this->faker->userName,
        ])->assertExitCode(1);
    }

    /**
     * @test add a user to an organization with malformed email
     */
    public function test_add_a_user_to_an_organization_with_malformed_email() {
        $organization = factory(Organization::class)->create();
        $this->artisan("organization:adduser",[
            "organizationId" => $organization->uuid,
            "firstName" => $this->faker->name,
            "lastName" => $this->faker->lastName,
            "email" => $this->faker->userName,
            "username" => $this->faker->userName,
        ])->assertExitCode(1);
    }

    /**
     * @test to an organization add a user with invalid username
     */
    public function test_to_an_organization_add_user_with_invalid_username() {
        $organization = factory(Organization::class)->create();
        $this->artisan("organization:adduser",[
            "organizationId" => $organization->uuid,
            "firstName" => $this->faker->name,
            "lastName" => $this->faker->lastName,
            "email" => $this->faker->userName,
            "username" => " ",
        ])->assertExitCode(1);
    }

    /**
     * @test to an organization add a user with invalid last name
     */
    public function test_to_an_organization_add_user_with_invalid_lastname() {
        $organization = factory(Organization::class)->create();
        $this->artisan("organization:adduser",[
            "organizationId" => $organization->uuid,
            "firstName" => $this->faker->name,
            "lastName" => " ",
            "email" => $this->faker->email,
            "username" => $this->faker->userName,
        ])->assertExitCode(1);
    }

    /**
     * @test to an organization add a user with invalid first name
     */
    public function test_to_an_organization_add_user_with_invalid_firstname() {
        $organization = factory(Organization::class)->create();
        $this->artisan("organization:adduser",[
            "organizationId" => $organization->uuid,
            "firstName" => " ",
            "lastName" => $this->faker->lastName,
            "email" => $this->faker->email,
            "username" => $this->faker->userName,
        ])->assertExitCode(1);
    }

    /**
     * @test add a user to an organization where auth service returns an error
     */
    public function test_to_an_organization_add_a_user_where_auth_service_returns_an_error() {
        $users = User::count();
        app()->bind(KeycloakClientInterface::class,
            function($app)  {
                return \Mockery::mock(KeycloakClientInterface::class, function($mock) {
                    $mock->shouldReceive("createUser")->andThrows(
                        new \Exception()
                    );
                });
            }
        );

        $organization = factory(Organization::class)->create();
        $this->artisan("organization:adduser",[
            "organizationId" => $organization->uuid,
            "firstName" => $this->faker->name,
            "lastName" => $this->faker->lastName,
            "email" => $this->faker->email,
            "username" => $this->faker->userName,
            "--yes" => true,
        ])->assertExitCode(1);

        $this->assertEquals($users, User::count());
    }

}
