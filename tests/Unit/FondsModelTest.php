<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Fonds;
use App\Archive;
use Webpatser\Uuid\Uuid;


class FondsModelTest extends TestCase
{
    use DatabaseTransactions;

    private $fondsCount;
    private $valid_top_level_fonds_data;
    private $owner_archive;
    private $markedForDeletion;

    public function setUp() : void
    {
        parent::setUp();

        $this->fondsCount = Fonds::count();

        $this->owner_archive = Archive::create([
            'title' => 'FondsModelTestArchive',
            'description' => "A few words",
            'parent_uuid' => null
        ]);

        $this->valid_top_level_fonds_data = [
            'title' => "test fonds model title",
            'owner_archive_uuid' => $this->owner_archive->uuid
        ];

        $this->topLevelFonds = Fonds::create( $this->valid_top_level_fonds_data );

        $this->valid_subfonds_data = [
            'title' => "test subfonds model title",
            'owner_archive_uuid' => $this->owner_archive->uuid,
            'parent_id' => $this->topLevelFonds->id
        ];

        $this->more_valid_subfonds_data = [
            'title' => "test more subfonds model title",
       ] + $this->valid_subfonds_data;

        $this->even_more_valid_subfonds_data = [
            'title' => "test even more subfonds model title",
        ] + $this->valid_subfonds_data;
    }

    public function test_it_creates_top_level_fonds_by_default()
    {
        $fonds = Fonds::create( $this->valid_top_level_fonds_data );

        $this->assertNotNull( $fonds );
        $this->assertEmpty( $fonds->parent()->get() );
    }

    public function test_when_only_one_top_level_fonds_has_been_created_it_has_no_siblings()
    {
        $this->markTestSkipped('needs empty database');
        $this->assertCount( 0, $this->topLevelFonds->siblings()->get() );
    }

    public function test_when_the_second_top_level_fonds_have_been_created_it_has_one_sibling()
    {
        $this->markTestSkipped('needs empty database');
        $fonds = Fonds::create( $this->valid_top_level_fonds_data );
        $this->assertCount( 1, $fonds->siblings()->get() );
    }

    public function test_the_first_created_subfonds_has_no_other_siblings()
    {
        $fonds = Fonds::create( $this->valid_subfonds_data );
        $this->assertCount( 0, $fonds->siblings()->get() );
    }

    public function test_it_throws_when_creating_a_fonds_without_a_valid_archive()
    {
        $this->expectException( \InvalidArgumentException::class );

        Fonds::create( ['owner_archive_uuid' => 'b0c7100d-2920-4340-a09c-e5ccee0fa90e'] + $this->valid_top_level_fonds_data );
    }

    public function test_when_assigning_a_subfonds_to_a_fonds_the_relation_is_set()
    {
        $fonds = Fonds::create( $this->valid_subfonds_data );

        $this->assertEquals($fonds->parent->get(), $this->topLevelFonds->get());
    }

    public function test_when_assigning_a_subfonds_to_a_fonds_it_is_found_in_its_subFonds()
    {
        $fonds = Fonds::create( $this->valid_subfonds_data );

        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds));
    }

    public function test_when_assigning_multiple_subfonds_to_a_fonds_they_are_found_in_its_subFonds()
    {
        $fonds1 = Fonds::create( $this->valid_subfonds_data );
        $fonds2 = Fonds::create( $this->more_valid_subfonds_data );
        $fonds3 = Fonds::create( $this->even_more_valid_subfonds_data );
        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds1));
        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds2));
        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds3));
    }


    public function test_when_creating_a_new_subfonds_given_no_position_is_set_it_is_added_last()
    {
        $fonds1 = Fonds::create( $this->valid_subfonds_data );
        $fonds2 = Fonds::create( $this->more_valid_subfonds_data );
        $fonds3 = Fonds::create( $this->even_more_valid_subfonds_data );
        $this->assertEquals( 1, $fonds1->position);
        $this->assertEquals( 2, $fonds2->position);
        $this->assertEquals( 3, $fonds3->position);
    }

    public function test_it_throws_if_trying_to_move_to_a_non_existant_fonds()
    {
        $this->expectException( ModelNotFoundException::class );
        $fonds1 = Fonds::create( $this->valid_subfonds_data );

        $fonds1->moveBefore(PHP_INT_MAX);
    }

    public function test_when_moving_a_subfonds_before_the_first_the_structure_remains_intact()
    {
        $fonds1 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data );
        $fonds2 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data );
        $fonds3 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data );

        $fonds2->moveBefore($fonds1->id);
        $fonds2->refresh();
        $fonds1->refresh();
        $fonds3->refresh();
        $this->assertEquals( 0, $fonds2->position );
        $this->assertEquals( 1, $fonds1->position );
        $this->assertEquals( 2, $fonds3->position );
    }

    public function test_when_moving_a_subfonds_before_the_last_the_structure_remains_intact()
    {
        $fonds1 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data );
        $fonds2 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data );
        $fonds3 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data );

        $fonds1->moveBefore($fonds3->id);
        $fonds2->refresh();
        $fonds1->refresh();
        $fonds3->refresh();
        $this->assertEquals( 0, $fonds2->position );
        $this->assertEquals( 1, $fonds1->position );
        $this->assertEquals( 2, $fonds3->position );
    }

    public function test_when_moving_a_subfonds_after_the_first_the_structure_remains_intact()
    {
        $fonds1 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data );
        $fonds2 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data );
        $fonds3 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data );

        $fonds3->moveAfter($fonds1->id);
        $fonds1->refresh();
        $fonds2->refresh();
        $fonds3->refresh();
        $this->assertEquals( 0, $fonds1->position );
        $this->assertEquals( 1, $fonds3->position );
        $this->assertEquals( 2, $fonds2->position );
    }


    public function test_when_moving_a_subfonds_after_the_last_the_structure_remains_intact()
    {
        $fonds1 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data );
        $fonds2 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data );
        $fonds3 = Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data );

        $fonds1->moveAfter( $fonds3->id );
        $fonds1->refresh();
        $fonds2->refresh();
        $fonds3->refresh();
        $this->assertEquals( 0, $fonds2->position );
        $this->assertEquals( 1, $fonds3->position );
        $this->assertEquals( 2, $fonds1->position );
    }


}
