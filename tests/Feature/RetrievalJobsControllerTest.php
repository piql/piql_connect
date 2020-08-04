<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;
use App\Metadata;
use App\User;
use App\Job;


class RetrievalJobsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $job;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->job = factory(Job::class)->create();
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $this->job->metadata()->save($metadata);
    }


    public function test_when_requesting_an_existing_job_it_responds_200()
    {
        $response = $this->get( route('storage.jobs', $this->job->id ));
        $response->assertStatus(200);
    }

    public function test_when_requesting_a_job_with_an_id_the_returned_job_has_the_correct_id()
    {
        $response = $this->get( route('storage.jobs', $this->job->id ));
        $response->assertJson([ 'data' => [ 'id' => $this->job->id] ]);
    }

}
