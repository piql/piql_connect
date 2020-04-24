<?php

namespace Tests\Feature;

use App\Archive;
use App\Bag;
use App\Events\BagFilesEvent;
use App\Events\PreProcessBagEvent;
use App\File;
use App\Holding;
use App\Interfaces\IngestValidationInterface;
use App\Interfaces\PreProcessBagInterface;
use App\Listeners\PreProcessBagListener;
use App\Services\FMUIngestService;
use App\Services\IngestValidationService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use App\User;

class PreProcessBagTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;
    private $bag;

    public function setUp() : void
    {
        parent::setUp();
        config( ["piql_connect.service.ingest_validation" => "App\Services\IngestValidationService"]);
        config( ["piql_connect.service.pre_process_bag" => "App\Services\PreProcessBagService"]);

        $this->testUser = factory( User::class)->create();
        Passport::actingAs( $this->testUser );

        $archive = Archive::create([
            'title' => 'FMU',
            'description' => "A few words",
            'parent_uuid' => null
        ]);

        Holding::create([
            'title' => "000000-100000",
            'owner_archive_uuid' => $archive->uuid
        ]);

        Holding::create([
            'title' => "400000-400000",
            'owner_archive_uuid' => $archive->uuid
        ]);

        $bag = factory(Bag::class)->create([
            "status" => "closed"
        ]);

        $this->bag = $bag->fresh();


    }

    public function test_pre_process_bag_with_multiple_files()
    {
        $bagId = $this->bag->id;
        factory(File::class)->create([
            'filename' => "FMU.000000.tif",
            'bag_id' => $bagId
        ]);

        factory(File::class)->create([
            'filename' => "FMUA.000000.tif",
            'bag_id' => $bagId
        ]);

        factory(File::class)->create([
            'filename' => "FMU.000000.jpg",
            'bag_id' => $bagId
        ]);

        factory(File::class)->create([
            'filename' => "FMU.400000-1.tif",
            'bag_id' => $bagId
        ]);

        factory(File::class)->create([
            'filename' => "FMUA.400000 479087234.tif",
            'bag_id' => $bagId
        ]);

        Event::fake();
        $event = new PreProcessBagEvent($this->bag);
        $listener = new PreProcessBagListener($this->app->make(\App\Interfaces\PreProcessBagInterface::class));

        //test
        $listener->handle($event);

        Event::assertDispatched(BagFilesEvent::class, function($event) {
            return $event->bag->files()->count() == 5;
        });
        Event::assertDispatched(BagFilesEvent::class, 1);

    }

    public function test_validate_files() {
        $service = new IngestValidationService($this->app);
        self::assertTrue($service->validateFileName("FMU.800000.tif"));
    }

    public function test_instantiate_default_pre_process_service_class() {
        $service = $this->app->make( PreProcessBagInterface::class );
        $this->assertNotInstanceOf( FMUIngestService::class, $service );
    }

    public function test_instantiate_default_ingest_validation_service_class() {
        $service = $this->app->make( IngestValidationInterface::class );
        $this->assertNotInstanceOf( FMUIngestService::class, $service );
    }


}
