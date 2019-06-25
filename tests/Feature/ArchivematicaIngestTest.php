<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArchivematicaIngestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanListInstances()
    {
        $response = $this->get('/api/v1/ingest/am/instances');

        $response->assertStatus(200);
    }
}
