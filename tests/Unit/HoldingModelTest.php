<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Holding;
use App\Archive;
use Webpatser\Uuid\Uuid;


class HoldingModelTest extends TestCase
{
    use DatabaseTransactions;

    private $holdingCount;
    private $valid_top_level_holding_data;
    private $owner_archive;
    private $markedForDeletion;

    public function setUp() : void
    {
        parent::setUp();

        $this->holdingCount = Holding::count();

        $this->owner_archive = Archive::create([
            'title' => 'HoldingModelTestArchive',
            'description' => "A few words",
            'parent_uuid' => null
        ]);

        $this->valid_top_level_holding_data = [
            'title' => "test holding model title",
            'owner_archive_uuid' => $this->owner_archive->uuid
        ];

        $this->topLevelHolding = Holding::create( $this->valid_top_level_holding_data );

        $this->valid_subholding_data = [
            'title' => "test subholding model title",
            'owner_archive_uuid' => $this->owner_archive->uuid,
            'parent_id' => $this->topLevelHolding->id
        ];

        $this->more_valid_subholding_data = [
            'title' => "test more subholding model title",
       ] + $this->valid_subholding_data;

        $this->even_more_valid_subholding_data = [
            'title' => "test even more subholding model title",
        ] + $this->valid_subholding_data;
    }

    public function test_it_creates_top_level_holding_by_default()
    {
        $holding = Holding::create( $this->valid_top_level_holding_data );

        $this->assertNotNull( $holding );
        $this->assertEmpty( $holding->parent()->get() );
    }

    public function test_the_first_created_subholding_has_no_other_siblings()
    {
        $holding = Holding::create( $this->valid_subholding_data );
        $this->assertCount( 0, $holding->siblings()->get() );
    }

    public function test_it_throws_when_creating_a_holding_without_a_valid_archive()
    {
        $this->expectException( \InvalidArgumentException::class );

        Holding::create( ['owner_archive_uuid' => 'b0c7100d-2920-4340-a09c-e5ccee0fa90e'] + $this->valid_top_level_holding_data );
    }

    public function test_when_assigning_a_subholding_to_a_holding_the_relation_is_set()
    {
        $holding = Holding::create( $this->valid_subholding_data );

        $this->assertEquals($holding->parent->get(), $this->topLevelHolding->get());
    }

    public function test_when_assigning_a_subholding_to_a_holding_it_is_found_in_its_subHolding()
    {
        $holding = Holding::create( $this->valid_subholding_data );

        $this->assertTrue($this->topLevelHolding->subHolding()->get()->contains($holding));
    }

    public function test_when_assigning_multiple_subholding_to_a_holding_they_are_found_in_its_subHolding()
    {
        $holding1 = Holding::create( $this->valid_subholding_data );
        $holding2 = Holding::create( $this->more_valid_subholding_data );
        $holding3 = Holding::create( $this->even_more_valid_subholding_data );
        $this->assertTrue($this->topLevelHolding->subHolding()->get()->contains($holding1));
        $this->assertTrue($this->topLevelHolding->subHolding()->get()->contains($holding2));
        $this->assertTrue($this->topLevelHolding->subHolding()->get()->contains($holding3));
    }


    public function test_when_creating_a_new_subholding_given_no_position_is_set_it_is_added_last()
    {
        $holding1 = Holding::create( $this->valid_subholding_data );
        $holding2 = Holding::create( $this->more_valid_subholding_data );
        $holding3 = Holding::create( $this->even_more_valid_subholding_data );
        $this->assertEquals( 1, $holding1->position);
        $this->assertEquals( 2, $holding2->position);
        $this->assertEquals( 3, $holding3->position);
    }

    public function test_it_throws_if_trying_to_move_to_a_non_existant_holding()
    {
        $this->expectException( ModelNotFoundException::class );
        $holding1 = Holding::create( $this->valid_subholding_data );

        $holding1->moveBefore(PHP_INT_MAX);
    }

    public function test_when_moving_a_subholding_before_the_first_the_structure_remains_intact()
    {
        $holding1 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->valid_subholding_data );
        $holding2 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->more_valid_subholding_data );
        $holding3 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->even_more_valid_subholding_data );

        $holding2->moveBefore($holding1->id);
        $holding2->refresh();
        $holding1->refresh();
        $holding3->refresh();
        $this->assertEquals( 0, $holding2->position );
        $this->assertEquals( 1, $holding1->position );
        $this->assertEquals( 2, $holding3->position );
    }

    public function test_when_moving_a_subholding_before_the_last_the_structure_remains_intact()
    {
        $holding1 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->valid_subholding_data );
        $holding2 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->more_valid_subholding_data );
        $holding3 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->even_more_valid_subholding_data );

        $holding1->moveBefore($holding3->id);
        $holding2->refresh();
        $holding1->refresh();
        $holding3->refresh();
        $this->assertEquals( 0, $holding2->position );
        $this->assertEquals( 1, $holding1->position );
        $this->assertEquals( 2, $holding3->position );
    }

    public function test_when_moving_a_subholding_after_the_first_the_structure_remains_intact()
    {
        $holding1 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->valid_subholding_data );
        $holding2 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->more_valid_subholding_data );
        $holding3 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->even_more_valid_subholding_data );

        $holding3->moveAfter($holding1->id);
        $holding1->refresh();
        $holding2->refresh();
        $holding3->refresh();
        $this->assertEquals( 0, $holding1->position );
        $this->assertEquals( 1, $holding3->position );
        $this->assertEquals( 2, $holding2->position );
    }


    public function test_when_moving_a_subholding_after_the_last_the_structure_remains_intact()
    {
        $holding1 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->valid_subholding_data );
        $holding2 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->more_valid_subholding_data );
        $holding3 = Holding::create( ['parent_id' => $this->topLevelHolding->id] + $this->even_more_valid_subholding_data );

        $holding1->moveAfter( $holding3->id );
        $holding1->refresh();
        $holding2->refresh();
        $holding3->refresh();
        $this->assertEquals( 0, $holding2->position );
        $this->assertEquals( 1, $holding3->position );
        $this->assertEquals( 2, $holding1->position );
    }


}
