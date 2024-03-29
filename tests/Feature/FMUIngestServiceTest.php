<?php

namespace Tests\Unit;

use App\Collection;
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
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FMUIngestServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $bag;
    private $regex;

    protected function setUp(): void
    {
        $this->markTestSkipped('FMU Ingest cannot work for the universal case');
        parent::setUp(); // TODO: Change the autogenerated stub
        config( ["piql_connect.service.pre_process_bag" => "App\Services\FMUIngestService"]);
        config( ["piql_connect.service.ingest_validation" => "App\Services\FMUIngestService"]);

        $user = factory(User::class)->create();
        Passport::actingAs( $user );

        $collection = Collection::create([
            'title' => '(FMU)',
            'description' => "A few words"
        ]);

        Holding::create([
            'title' => "000000-100000",
            'collection_uuid' => $collection->uuid
        ]);

        Holding::create([
            'title' => "400000-400000",
            'collection_uuid' => $collection->uuid
        ]);

        $bag = factory(Bag::class)->create([
            "owner" => $user->id,
            "status" => "closed"
        ]);

        $this->bag = $bag->fresh();
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function test_split_files_into_seperate_bags() {
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

        $service = new FMUIngestService($this->app);
        $bags = $service->process($this->bag);
        self::assertEquals(3, count($bags));
        self::assertEquals(3, $bags[0]->files()->count());
        self::assertEquals(1, $bags[1]->files()->count());
        self::assertEquals(1, $bags[2]->files()->count());
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
            return $event->bag->files()->count() > 0;
        });
        Event::assertDispatched(BagFilesEvent::class, 3);

    }

    public function test_validate_files_with_valid_filenames() {
        $service = new FMUIngestService($this->app);
        self::assertTrue($service->validateFileName("FMUA.000000.tif"));
    }

    public function test_validate_files_with_invalid_filenames() {
        $service = new FMUIngestService($this->app);
        self::assertFalse($service->validateFileName("FM.000000.tif"));
    }

    public function test_validate_files_with_invalid_collection() {
        $service = new FMUIngestService($this->app);
        self::assertFalse($service->validateFileName("ABC.800000.tif"));
    }

    public function test_validate_files_with_invalid_holding() {
        $service = new FMUIngestService($this->app);
        self::assertFalse($service->validateFileName("FMU.800000.tif"));
    }

    public function test_instantiate_correct_pre_process_service_class() {
        $service = $this->app->make( PreProcessBagInterface::class );
        $this->assertInstanceOf( FMUIngestService::class, $service );
    }

    public function test_instantiate_correct_ingest_validation_service_class() {
        $service = $this->app->make( IngestValidationInterface::class );
        $this->assertInstanceOf( FMUIngestService::class, $service );
    }

}
