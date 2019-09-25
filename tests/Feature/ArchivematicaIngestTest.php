<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class ArchivematicaIngestTest extends TestCase
{

    private $testUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->testUser = factory(\App\User::class )->create();
        Passport::actingAs( $this->testUser );
    }

    public function tearDown() : void
    {
        $this->testUser->delete();
    }

    public function testCanListInstances()
    {
        $response = $this->get('/api/v1/ingest/am/instances');

        $response->assertStatus(200);
    }
}
