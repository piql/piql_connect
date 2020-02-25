<?php

namespace Tests\Feature;

use App\Events\InformationPackageUploaded;
use App\Listeners\AddAipToBucketListener;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;
use Faker\Factory as Faker;
use App\Archive;
use App\Job;

class AddAipToBucketListenerTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;
    private $aips;
    private $job;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class)->create();
        Passport::actingAs( $this->testUser );

        $this->job = Job::currentJob($this->testUser->id);

        $this->aips = factory(\App\Aip::class, 2)->state("dummyData")->create([
            "owner" => $this->testUser->id,
        ])->each(function($aip) {
            $file = factory(\App\FileObject::class)->state("dummyData")->create([
               "size" => $this->job->bucketSize,
            ]);
            $aip->fileObjects()->save($file);
        });


    }

    public function test_add_aip_to_empty_bucket()
    {
        $listener = new AddAipToBucketListener();
        $listener->handle( new InformationPackageUploaded($this->aips[1]));
        $this->assertEquals(1, Job::where('owner', $this->testUser->id)->count());
    }

    public function test_add_aip_to_full_bucket()
    {
        $this->job->aips()->save($this->aips[0]);
        $listener = new AddAipToBucketListener();
        $listener->handle( new InformationPackageUploaded($this->aips[1]));
        $this->assertEquals(2, Job::where('owner', $this->testUser->id)->count());
    }

}
