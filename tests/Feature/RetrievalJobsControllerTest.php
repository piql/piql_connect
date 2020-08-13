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
use App\Aip;
use App\StorageProperties;
use App\FileObject;


class RetrievalJobsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $jobs;
    private $firstJob;

    const JOBS_CREATED = 3;
    const AIPS_PER_JOB_CREATED = 4;
    const FILES_PER_AIP_CREATED = 2;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory( User::class )->create();
        Passport::actingAs( $this->user );

        $this->jobs = factory( Job::class, self::JOBS_CREATED )
                             ->states( 'ingesting' )
                             ->create();
        
        $this->jobs->each( function ( $job ) {
            $aips = factory( Aip::class, self::AIPS_PER_JOB_CREATED )
                ->states( 'dummyData' )
                ->create();
            
            $aips->each( function ($aip) use($job) {

                $sp = factory( StorageProperties::class )->create();
                $sp->aip_uuid = $aip->external_uuid;
                $sp->save();

                $aip->owner = $this->user->id;
                $aip->save();
                $aip->storage_properties()
                    ->associate( $sp )
                    ->save();

                $files = factory( FileObject::class, self::FILES_PER_AIP_CREATED )
                    ->states('dummyData')
                    ->create();
                $files->each( function ( $file ) use( $aip ) {
                    $aip->fileObjects()->save( $file );
                });

                $job->aips()->save( $aip );
                $metadata = factory( Metadata::class )->create([
                    "modified_by" => $this->user->id,
                ]);
                $job->metadata()->save( $metadata );

                $job->owner = $this->user->id;
                $job->save();
            });
        });

        $this->firstJob = $this->jobs[0];
    }


    public function test_when_requesting_an_existing_job_it_responds_200()
    {
        $response = $this->get( route('storage.buckets', $this->firstJob->id ));
        $response->assertStatus(200);
    }

    public function test_when_requesting_a_job_with_an_id_the_returned_job_has_the_correct_id()
    {
        $response = $this->get( route('storage.buckets', $this->firstJob->id ));
        $response->assertJson([ 'data' => [ 'id' => $this->firstJob->id] ]);
    }


    public function test_when_requesting_jobs_index_it_responds_200()
    {
        $response = $this->get( route('storage.buckets.aips'));
        $response->assertStatus(200);
    }

    public function test_when_requesting_job_index_it_responds_with_a_list_of_jobs_with_aips_owned_by_current_user()
    {
        $response = $this->get( route('storage.buckets.aips' ));
        $result = $response->decodeResponseJson();
        /* We should see exactly the number of jobs we created, because the api checks for ownership */
        $aipsCreated = self::JOBS_CREATED * self::AIPS_PER_JOB_CREATED;
        /* Handle pagination */
        $aipsReturnedByApi = min( env("DEFAULT_ENTRIES_PER_PAGE"), $aipsCreated );

        $this->assertTrue( $result['meta']['total'] == $aipsCreated ); 
        $this->assertTrue( count( $result['data'] ) == $aipsReturnedByApi ) ;
    } 
}
