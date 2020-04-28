<?php

namespace Tests\Unit;

use App\Interfaces\MetadataGeneratorInterface;
use App\Services\DublinCoreCSVMetadataWriter;
use App\Services\DublinCoreJsonMetadataWriter;
use App\Services\MetadataGeneratorService;
use Tests\TestCase;

class MetadataGeneratorServiceTest extends TestCase
{

    /* refactor this when more formats are supported */
    public function test_when_injecting_an_instance_of_MetadataGeneratorInterface_it_makes_a_MetadataGeneratorService()
    {
        $service = $this->app->make( MetadataGeneratorInterface::class );
        $this->assertInstanceOf( MetadataGeneratorService::class, $service );
    }

    public function test_when_creating_cvs_writer_it_makes_a_DublinCoreJsonMetadataWriter()
    {
        $service = $this->app->make( MetadataGeneratorInterface::class );
        $this->assertInstanceOf( DublinCoreJsonMetadataWriter::class, $service->createMetadataWriter([
            'filename' => "metadata.json",
            'type' => 'json',
        ]));
    }

    public function test_when_creating_cvs_writer_it_makes_a_DublinCoreCsvMetadataWriter()
    {
        $service = $this->app->make( MetadataGeneratorInterface::class );
        $this->assertInstanceOf( DublinCoreCSVMetadataWriter::class, $service->createMetadataWriter([
            'filename' => "metadata.csv",
            'type' => 'csv',
        ]));
    }
}
