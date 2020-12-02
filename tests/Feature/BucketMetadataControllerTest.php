<?php

namespace Tests\Feature;

use App\Bag;
use App\Job;
use App\Http\Resources\MetadataResource;
use App\Metadata;
use App\User;
use App\Account;
use App\Organization;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;

class BucketMetadataControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $job;

    public function setUp() : void
    {
        parent::setUp();

        $organization = factory( Organization::class )->create();
        factory( Account::class )->create(['organization_uuid' => $organization->uuid]);
        $this->user = factory(User::class)->create(['organization_uuid' => $organization->uuid]);

        Passport::actingAs( $this->user );

        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);

        $this->job = factory(Job::class)->create([
        ]);

        $this->job->metadata()->save($metadata);
    }

    public function test_given_an_authenticated_user_when_getting_all_metadata_it_responds_200()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.bucket.metadata.index', $this->job->id) );
        $response->assertStatus( 200 );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_on_a_closed_bucket_it_responds_400()
    {
        $this->job->status="closed";
        $this->job->save();
        $response = $this->actingAs( $this->user )
            ->post( route('api.ingest.bucket.metadata.store', $this->job->id));
        $response->assertStatus( 400 );
    }

    public function test_given_an_authenticated_user_when_storing_metadata_on_a_created_bucket_responds_200()
    {
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $response = $this->actingAs( $this->user )
            ->json('POST', route('api.ingest.bucket.metadata.store', $this->job->id),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_on_a_open_bucket_it_responds_200()
    {
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.bucket.metadata.update', [$this->job->id, $this->job->metadata[0]->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 200 );

    }

    public function test_given_an_authenticated_user_when_updating_metadata_on_a_closed_bucket_it_responds_400()
    {
        $this->job->status="closed";
        $this->job->save();
        $metadata = factory(Metadata::class)->create([
            "modified_by" => $this->user->id,
        ]);
        $metadata->metadata = ['dc:title' => "Tøv"];
        $response = $this->actingAs( $this->user )
            ->json('PATCH', route('api.ingest.bucket.metadata.update', [$this->job->id, $this->job->metadata[0]->id]),
                (new MetadataResource($metadata))->toArray(null));
        $response->assertStatus( 400 );

    }

}
