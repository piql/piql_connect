<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;
use App\Holding;

class HoldingModelTest extends TestCase
{
    private $valid_holding_data = [
        'title' => 'HoldingModelTest', 
        'description' => "A few words",
        'parent_uuid' => null
    ];

    private $holding;
    private $validParentHolding;

    public function setUp() : void
    {
        parent::setUp();

        $this->validParentHolding = Holding::create([
            'title' => 'TestParentHolding',
            'parent_uuid' => null
        ]);
    }

    public function tearDown() : void
    {
        $this->validParentHolding->delete();

        try 
        {
            if($this->holding){
                $this->holding->delete();
            }
        } 
        catch(\Exception $ex)
        {
            print ("Could not clean up holding: ".$ex);
        }

        parent::tearDown();
    }


    public function test_it_is_assigned_an_uuid_when_created()
    {
        $this->holding = Holding::create($this->valid_holding_data);
        $this->assertRegExp("~".Uuid::VALID_UUID_REGEX."~", $this->holding->uuid);
    }

    public function test_when_creating_given_an_empty_title_it_throws()
    {
        $this->expectException("Exception");
        $this->holding = Holding::create();
    }

    public function test_when_creating_given_an_empty_description_it_is_created()
    {
        $this->holding = Holding::create(['description' => '']+$this->valid_holding_data);
        $this->assertNotNull($this->holding);
    }

    public function test_when_creating_given_an_emtpy_parent_uuid_it_is_created()
    {
        $this->holding = Holding::create(['parent_uuid' => ""]+$this->valid_holding_data);
        $this->assertNotNull($this->holding);
    }

    public function test_when_creating_given_a_parent_uuid_that_does_not_exist_it_throws()
    {
        $this->expectException("Exception");
        $this->holding = Holding::create(['parent_uuid' => Uuid::generate()]+$this->valid_holding_data);
    }

}
