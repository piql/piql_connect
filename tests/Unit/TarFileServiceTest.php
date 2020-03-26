<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Interfaces\FileCollectorInterface;
use App\Services\TarFileService;

class TarFileServiceTest extends TestCase
{

    /* refactor this when more formats are supported */
    public function test_when_injecting_an_instance_of_FileCollectorInterface_it_makes_a_TarFileService()
    {
        $service = $this->app->make( FileCollectorInterface::class );
        $this->assertInstanceOf( TarFileService::class, $service );
    }
}
