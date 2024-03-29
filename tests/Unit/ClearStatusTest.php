<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ClearIngestStatusEvent;
use App\Events\ClearTransferStatusEvent;
use App\File;
use App\Interfaces\ArchivematicaDashboardClientInterface;
use App\Listeners\ClearIngestStatus;
use App\Listeners\ClearTransferStatus;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;


class ClearStatusTest extends TestCase
{
    use DatabaseTransactions;

    protected $bag;
    protected $SIPName;
    protected $UUID;
    protected $amClient;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = factory(User::class)->create();
        Passport::actingAs( $user );
        $bag = factory(Bag::class)->create([
            "status" => "complete"
        ]);

        factory(File::class, 1)->create()->each(function($file) use ($bag) {
            $file->bag_id = $bag->id;
            $file->save();
        });

        $this->bag = $bag->fresh();
    }


    /**
     * @test
     */
    public function test_clear_tansfer_status() {
        // setup
        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('hideTransferStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'removed' => 'true',
                ],
                'statusCode' => 200,
            ]
        );

        $listener = new ClearTransferStatus($amClient);
        $event = new ClearTransferStatusEvent($this->bag);

        //test
        $listener->handle($event);
    }

    public function test_clear_ingest_status() {
        // setup

        $amClient = \Mockery::mock(ArchivematicaDashboardClientInterface::class);
        $amClient->shouldReceive('hideIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'removed' => 'true',
                ],
                'statusCode' => 200,
            ]
        );


        $listener = new ClearIngestStatus($amClient);
        $event = new ClearIngestStatusEvent($this->bag);

        //test
        $listener->handle($event);
    }

}
