<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webpatser\Uuid\Uuid;
use App\Collection;

class CollectionModelTest extends TestCase
{
    use DatabaseTransactions;

    private $valid_collection_data = [
        'title' => 'CollectionModelTest',
        'description' => "A few words"
    ];

    public function setUp() : void
    {
        parent::setUp();
    }

    public function test_it_is_assigned_an_uuid_when_created()
    {
        $collection = Collection::create($this->valid_collection_data);
        $this->assertRegExp("~".Uuid::VALID_UUID_REGEX."~", $collection->uuid);
    }

    public function test_when_creating_given_an_empty_title_it_throws()
    {
        $this->expectException("Exception");
        Collection::create();
    }

    public function test_when_creating_given_an_empty_description_it_is_created()
    {
        $collection = Collection::create(['description' => '']+$this->valid_collection_data);
        $this->assertNotNull($collection);
    }

}
