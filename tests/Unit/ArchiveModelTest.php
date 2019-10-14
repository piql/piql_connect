<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webpatser\Uuid\Uuid;
use App\Archive;

class ArchiveModelTest extends TestCase
{
    use DatabaseTransactions;

    private $valid_archive_data = [
        'title' => 'ArchiveModelTest', 
        'description' => "A few words",
        'parent_uuid' => null
    ];

    private $archive;
    private $validParentArchive;

    public function setUp() : void
    {
        parent::setUp();

        $this->validParentArchive = Archive::create([
            'title' => 'TestParentArchive',
            'parent_uuid' => null
        ]);
    }

    public function test_it_is_assigned_an_uuid_when_created()
    {
        $this->archive = Archive::create($this->valid_archive_data);
        $this->assertRegExp("~".Uuid::VALID_UUID_REGEX."~", $this->archive->uuid);
    }

    public function test_when_creating_given_an_empty_title_it_throws()
    {
        $this->expectException("Exception");
        $this->archive = Archive::create();
    }

    public function test_when_creating_given_an_empty_description_it_is_created()
    {
        $this->archive = Archive::create(['description' => '']+$this->valid_archive_data);
        $this->assertNotNull($this->archive);
    }

    public function test_when_creating_given_an_emtpy_parent_uuid_it_is_created()
    {
        $this->archive = Archive::create(['parent_uuid' => ""]+$this->valid_archive_data);
        $this->assertNotNull($this->archive);
    }

    public function test_when_creating_given_a_parent_uuid_that_does_not_exist_it_throws()
    {
        $this->expectException("Exception");
        $this->archive = Archive::create(['parent_uuid' => Uuid::generate()]+$this->valid_archive_data);
    }

}
