<?php

namespace Tests\Unit;

use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;
use App\Console\Commands\OrganizationList;

class ArtisanCommandOrganizationListTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected function setUp() : void
    {
        parent::setUp();
    }

    /**
     * @test list organizations
     */
    public function test_list_organization() {
        $this->artisan("organization:create",["name" => "test".$this->faker->uuid])->assertExitCode(0);
        $this->artisan("organization:list")->assertExitCode(0)->run();
    }

}
