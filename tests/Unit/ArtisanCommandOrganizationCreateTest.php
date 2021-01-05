<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;
use App\Console\Commands\OrganizationCreate;

class ArtisanCommandOrganizationCreateTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected function setUp() : void
    {
        parent::setUp();
    }

    /** @test it has organization create command */
    public function it_has_organization_create_command()
    {
        $this->assertTrue(class_exists(OrganizationCreate::class));
    }

    /**
     * @test create a basic organization with an uuid and name
     */
    public function test_create_a_basic_organization() {
        $this->artisan("organization:create",["name" => "test".$this->faker->uuid])->assertExitCode(0);
    }

    /**
     * @test create a basic organization with no name given
     */
    public function test_create_a_basic_organization_no_name_given() {
        $this->expectException(RuntimeException::class);
        $this->artisan("organization:create")->assertExitCode(1);
    }

    /**
     * @test create a basic organization with an empty name
     */
    public function test_create_a_basic_organization_empty_name_given() {
        $this->artisan("organization:create",["name" => ""])->expectsOutput("No name given")->assertExitCode(1);
    }

    /**
     * @test create a basic organization with a name that already exists
     */
    public function test_create_a_basic_organization_with_a_name_that_already_exists() {
        $name = "test".$this->faker->uuid;
        $this->artisan("organization:create",["name" => $name])->assertExitCode(0);
        $this->artisan("organization:create",["name" => $name])->assertExitCode(1);
    }

}
