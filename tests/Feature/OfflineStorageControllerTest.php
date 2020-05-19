<?php

namespace Tests\Feature;

use App\Bag;
use App\Job;
use App\Http\Resources\MetadataResource;
use App\Metadata;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;

class OfflineStorageControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $job;
    private $aips;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Passport::actingAs( $this->user );

        $this->job = Job::currentJob($this->user->id);

        $this->aips = factory(\App\Aip::class, 2)->state("dummyData")->create([
            "owner" => $this->user->id,
        ])->each(function($aip) {
            $bag = factory(\App\Bag::class)->create([
                'owner' => $this->user->id
            ]);
            $dip = factory(\App\Dip::class)->state("dummyData")->create([
                'aip_external_uuid' => $aip->external_uuid,
                'owner' => $this->user->id
            ]);
            $bag->storage_properties->update([
                "aip_uuid" => $aip->external_uuid,
                "dip_uuid" => $dip->external_uuid,
            ]);
            $file = factory(\App\FileObject::class)->state("dummyData")->create([
                "size" => $this->job->bucketSize,
            ]);
            $aip->fileObjects()->save($file);
            $this->job->aips()->save($aip);
        });

    }

    public function test_given_an_authenticated_user_when_getting_a_jobs_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json( 'GET', route('api.ingest.bucket', $this->job->id) );
        $response->assertStatus( 200 )
            ->assertJson(['data' => ['id' => $this->job->id, 'archive_objects' => 2]]);
    }

    public function test_given_an_authenticated_user_when_getting_all_pending_jobs_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json( 'GET', route('api.ingest.buckets.pending') );
        $response->assertStatus( 200 )
            ->assertJson(['data' => [['id' => $this->job->id, 'aips_count' => 2]]]);
    }

    public function test_given_an_authenticated_user_when_getting_all_archiving_jobs_it_responds_200()
    {
        $this->job->update(['status' => 'ingesting']);
        $response = $this->actingAs( $this->user )
            ->json( 'GET', route('api.ingest.buckets.archiving', $this->job->id) );
        $response->assertStatus( 200 )
            ->assertJsonFragment(['id' => $this->job->id, 'status' => 'ingesting', 'archive_objects' => 2]);
    }

    public function test_given_an_authenticated_user_when_getting_all_content_from_a_given_jobs_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json( 'GET', route('api.ingest.bucket.dips', $this->job->id) );
        $response->assertStatus( 200 )
            ->assertJsonFragment(['id' => $this->aips[0]->storage_properties->dip->id])
            ->assertJsonFragment(['id' => $this->aips[1]->storage_properties->dip->id]);
    }

    public function test_given_an_authenticated_user_updating_bucket_name_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['name' => "bucket name"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['name' => "bucket name"]);
    }

    public function test_given_an_authenticated_user_updating_bucket_with_an_empty_name_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['name' => ""]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['name' => ""]);
    }

    public function test_given_an_authenticated_user_when_updating_a_created_bucket_with_status_ingesting_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['status' => "ingesting"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['status' => "ingesting"]);
    }

    public function test_given_an_authenticated_user_when_updating_a_created_bucket_with_invalid_status_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['status' => "closed"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['status' => "created"]);
    }

}
