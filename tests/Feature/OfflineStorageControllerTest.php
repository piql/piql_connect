<?php

namespace Tests\Feature;

use App\Bag;
use App\Job;
use App\Http\Resources\MetadataResource;
use App\Metadata;
use App\Auth\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Faker\Factory as faker;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;
use App\Mail\PiqlIt;

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
            $aip->update(["size" => $aip->fileObjects()->sum("size")]);
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
        $this->job->applyTransition('piql_it')->save();
        $response = $this->actingAs( $this->user )
            ->json( 'GET', route('api.ingest.buckets.archiving', $this->job->id) );
        $response->assertStatus( 200 )
            ->assertJsonFragment(['status' => 'transferring', 'archive_objects' => 2]);
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
                ['name' => "bucket name"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['name' => "bucket name"]);

        \Event::fake();

        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['status' => "commit"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['status' => "transferring"]);

        \Event::assertDispatched(\App\Events\CommitJobEvent::class, 1);
    }

    public function test_given_an_authenticated_user_when_updating_a_created_bucket_with_invalid_name_and_with_status_ingesting_it_responds_400()
    {
        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['name' => ":"]);
        $response->assertStatus( 400 );
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

    public function test_given_an_authenticated_user_and_deleting_a_valid_bucket_it_responds_204()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE',
                route('api.ingest.bucket.delete', [$this->job->id]));
        $response->assertStatus( 204 );
        $this->assertEquals( 0, \DB::table("archivables")->where("archive_id", $this->job->id)->count());
    }

    public function test_given_an_authenticated_user_and_deleting_an_invalid_bucket_it_responds_404()
    {
        $response = $this->actingAs( $this->user )
            ->json('DELETE',
                route('api.ingest.bucket.delete', [$this->job->id]));
        $response->assertStatus( 204 );

        $response = $this->actingAs( $this->user )
            ->json('DELETE',
                route('api.ingest.bucket.delete', [$this->job->id]));
        $response->assertStatus( 404 )
            ->assertJsonFragment(['error' => 404]);
    }

    public function test_given_an_authenticated_user_and_detaching_a_dip_from_a_valid_bucket_it_responds_204()
    {
        $aipCount = $this->job->aips()->count();
        $response = $this->actingAs( $this->user )
            ->json('DELETE',
                route('api.ingest.bucket.detach.dip', [$this->job->id, $this->aips[0]->storage_properties->dip->id]));
        $response->assertStatus( 204 );
        $this->assertEquals( $aipCount-1, \DB::table("archivables")->where("archive_id", $this->job->id)->count());
    }

    public function test_given_an_authenticated_user_and_detaching_a_dip_from_an_invalid_bucket_it_responds_404()
    {
        $aipCount = $this->job->aips()->count();
        $response = $this->actingAs( $this->user )
            ->json('DELETE',
                route('api.ingest.bucket.detach.dip', [Uuid::generate()->string, $this->aips[0]->storage_properties->dip->id]));
        $response->assertStatus( 404 )
            ->assertJsonFragment(['error' => 404]);
        $this->assertEquals( $aipCount, \DB::table("archivables")->where("archive_id", $this->job->id)->count());
    }


    public function test_given_an_authenticated_user_and_detaching_an_invalid_dip_from_a_bucket_it_responds_404()
    {
        $aipCount = $this->job->aips()->count();
        $response = $this->actingAs( $this->user )
            ->json('DELETE',
                route('api.ingest.bucket.detach.dip', [$this->job->id, Uuid::generate()->string]));
        $response->assertStatus( 404 )
            ->assertJsonFragment(['error' => 404]);
        $this->assertEquals( $aipCount, \DB::table("archivables")->where("archive_id", $this->job->id)->count());
    }

    public function test_send_a_email_when_piql_button_is_pressed()
    {
        $this->assertTrue((bool)env('PIQLIT_NOTIFY_EMAIL_TO'),
            "Environment variable 'PIQLIT_NOTIFY_EMAIL_TO' not set. Can't execute test");

        \Mail::fake();
        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['name' => "bucket name"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['name' => "bucket name"]);

        \Event::fake();

        $response = $this->actingAs( $this->user )
            ->json('PATCH',
                route('api.ingest.bucket.update', [$this->job->id]),
                ['status' => "commit"]);
        $response->assertStatus( 200 )
            ->assertJsonFragment(['status' => "transferring"]);
        \Event::assertDispatched(\App\Events\CommitJobEvent::class, 1);
        \Mail::assertSent(PiqlIt::class);
    }
}
