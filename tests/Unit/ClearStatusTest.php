<?php

namespace Tests\Unit;

use App\Bag;
use App\Events\ClearIngestStatusEvent;
use App\Events\ClearTransferStatusEvent;
use App\File;
use App\Listeners\ArchivematicaClient;
use App\Listeners\ClearIngestStatus;
use App\Listeners\ClearTransferStatus;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;


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
        $bag = factory(Bag::class)->create([
            "owner" => $user->id,
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
        $amClient = \Mockery::mock(ArchivematicaClient::class);
        $amClient->shouldReceive('getTransferStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched transfer status successfully.',
                    'results' =>  [
                        (object) [
                            'name' => $this->bag->BagFileNameNoExt(),
                            'type' => 'zipped bag',
                            'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                            'status' => 'COMPLETE'
                        ]
                    ]
                ],
                'statusCode' => 200,
            ]
        );

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

        $amClient = \Mockery::mock(ArchivematicaClient::class);
        $amClient->shouldReceive('getIngestStatus')->once()->andReturns(
            (object)[
                'contents' => (object) [
                    'message' => 'Fetched ingest status successfully.',
                    'results' =>  [
                        (object) [
                            'name' => $this->bag->BagFileNameNoExt(),
                            'type' => 'zipped bag',
                            'uuid' => 'b3a0a321-d857-4429-91ac-f28c6b9cb28d',
                            'status' => 'COMPLETE'
                        ]
                    ]
                ],
                'statusCode' => 200,
            ]
        );

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