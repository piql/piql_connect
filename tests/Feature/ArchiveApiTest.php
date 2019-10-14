<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;
use Faker\Factory as Faker;
use App\Archive;

class ArchiveApiTest extends TestCase
{
    use DatabaseTransactions;

    private $title1;
    private $description1;
    private $uuid1;
    private $validArchiveData;
    private $validArchiveDataCreated;
    private $createdArchiveIds;
    private $archiveCount;
    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );

        $this->createdArchiveIds = collect([]);
        $this->archiveCount = Archive::count();
        $faker = Faker::create();
        $this->title1 = $faker->company();
        $this->uuid1 = $faker->uuid();
        $this->description1 = $faker->text(100);
        $this->title2 = $faker->company();
        $this->uuid2 = $faker->uuid();
        $this->description2 = $faker->text(100);
        $this->validArchiveDataCreated = [
            'uuid' => $this->uuid1,
            'title' => $this->title1,
            'description' => $this->description1
        ];

        $this->validArchiveData = [
            'uuid' => $this->uuid2,
            'title' => $this->title2,
            'description' => $this->description2
        ];

        $this->createdTestArchive = Archive::create($this->validArchiveDataCreated);

        $this->createdArchiveIds->push( $this->createdTestArchive->id );
    }

    public function test_it_can_get_a_list_of_archives()
    {
        $response = $this->get( route( 'planning.archives.index' ) );
        $response->assertStatus( 200 );
    }

    public function test_when_requesting_a_list_of_archives_it_returns_a_valid_json_array()
    {
        $response = $this->get(route('planning.archives.index'));
        $response->assertJsonStructure(['data' => []]);
    }

    public function test_when_creating_an_archive_given_valid_data_it_responds_with_201()
    {
        $response = $this->json('POST',
            route('planning.archives.store'),
            $this->validArchiveData);
        $response->assertStatus(201);
    }

    public function test_when_creating_an_archive_given_valid_data_it_returns_a_valid_json_object()
    {
        $response = $this->json( 'POST',
            route( 'planning.archives.store' ),
            $this->validArchiveData );

        $response->assertJson([ 'data' => $this->validArchiveData ]);
    }

    public function test_given_an_archive_exists_when_deleting_it_it_is_soft_deleted()
    {
        $archiveId = $this->createdTestArchive->id;
        $response = $this->json( 'DELETE',
            route( 'planning.archives.destroy', ['id' => $archiveId] )
        );
        $response->assertStatus(204);
        $model = Archive::withTrashed()->find($archiveId);
        $this->assertNotNull($model->deleted_at);
    }

    public function test_given_an_archive_does_not_exist_when_deleting_it_responds_with_404_and_error_message()
    {
        $archiveId = PHP_INT_MAX;
        $response = $this->json( 'DELETE',
            route( 'planning.archives.destroy', ['id' => $archiveId] )
        );

        $response->assertStatus(404);
        $response->assertJson(['error' => 404, 'message' => 'No Archive with id '.$archiveId.' was found.']); 
    }

    public function test_given_an_archive_exists_when_requesting_it_it_is_returned()
    {
        $archiveId = $this->createdTestArchive->id;
        $response = $this->json( 'GET',
            route( 'planning.archives.show', ['id' => $archiveId] )
        );

        $response->assertStatus(200);
        $response->assertJson([ 'data' => $this->validArchiveDataCreated ]);
    }

    public function test_given_an_archive_does_not_exist_when_requesting_it_responds_with_a_404_and_error_message()
    {
        $archiveId = PHP_INT_MAX;
        $response = $this->json( 'GET',
            route( 'planning.archives.show', ['id' => $archiveId] )
        );

        $response->assertStatus(404);
        $response->assertJson(['error' => 404, 'message' => 'No Archive with id '.$archiveId.' was found.']); 
    }
}
