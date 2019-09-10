<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Fonds;
use App\Holding;
use Webpatser\Uuid\Uuid;


class FondsModelTest extends TestCase
{
    private $fondsCount;
    private $valid_top_level_fonds_data;
    private $owner_holding;
    private $markedForDeletion;

    public function setUp() : void
    {
        parent::setUp();

        $this->markedForDeletion = Array();
        $this->fondsCount = Fonds::count();

        $this->owner_holding = Holding::create([
            'title' => 'FondsModelTestHolding',
            'description' => "A few words",
            'parent_uuid' => null
        ]);

        $this->md($this->owner_holding);

        $this->valid_top_level_fonds_data = [
            'title' => "test fonds model title",
            'owner_holding_uuid' => $this->owner_holding->uuid
        ];

        $this->topLevelFonds = $this->md( Fonds::create( $this->valid_top_level_fonds_data ) );

        $this->valid_subfonds_data = [
            'title' => "test subfonds model title",
            'owner_holding_uuid' => $this->owner_holding->uuid,
            'parent_id' => $this->topLevelFonds->id
        ];

        $this->more_valid_subfonds_data = [
            'title' => "test more subfonds model title",
       ] + $this->valid_subfonds_data;

        $this->even_more_valid_subfonds_data = [
            'title' => "test even more subfonds model title",
        ] + $this->valid_subfonds_data;
    }

    public function tearDown() : void
    {
        array_map( function ($model) { $model->delete(); }, $this->markedForDeletion );

        $this->assertEquals( $this->fondsCount, Fonds::count() );
        parent::tearDown();
    }

    /* Mark an Eloquent model for deletion on tearDown
     *
     * returns the same objecct
     */

    private function md( Model $model )
    {
        array_push( $this->markedForDeletion, $model );
        return $model;
    }

    public function test_it_creates_top_level_fonds_by_default()
    {
        $fonds = $this->md(Fonds::create( $this->valid_top_level_fonds_data ));

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
        $fonds = $this->md(Fonds::create( $this->valid_top_level_fonds_data ));
        $this->assertCount( 1, $fonds->siblings()->get() );
    }

    public function test_the_first_created_subfonds_has_no_other_siblings()
    {
        $fonds = $this->md( Fonds::create( $this->valid_subfonds_data ) );
        $this->assertCount( 0, $fonds->siblings()->get() );
    }

    public function test_it_throws_when_creating_a_fonds_without_a_valid_holding()
    {
        $this->expectException( \InvalidArgumentException::class );

        Fonds::create( ['owner_holding_uuid' => 'b0c7100d-2920-4340-a09c-e5ccee0fa90e'] + $this->valid_top_level_fonds_data );
    }

    public function test_when_assigning_a_subfonds_to_a_fonds_the_relation_is_set()
    {
        $fonds = $this->md( Fonds::create( $this->valid_subfonds_data ) );

        $this->assertEquals($fonds->parent->get(), $this->topLevelFonds->get());
    }

    public function test_when_assigning_a_subfonds_to_a_fonds_it_is_found_in_its_subFonds()
    {
        $fonds = $this->md( Fonds::create( $this->valid_subfonds_data ) );

        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds));
    }

    public function test_when_assigning_multiple_subfonds_to_a_fonds_they_are_found_in_its_subFonds()
    {
        $fonds1 = $this->md(Fonds::create( $this->valid_subfonds_data ));
        $fonds2 = $this->md(Fonds::create( $this->more_valid_subfonds_data ));
        $fonds3 = $this->md(Fonds::create( $this->even_more_valid_subfonds_data ));
        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds1));
        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds2));
        $this->assertTrue($this->topLevelFonds->subFonds()->get()->contains($fonds3));
    }


    public function test_when_creating_a_new_subfonds_given_no_position_is_set_it_is_added_last()
    {
        $fonds1 = $this->md(Fonds::create( $this->valid_subfonds_data ));
        $fonds2 = $this->md(Fonds::create( $this->more_valid_subfonds_data ));
        $fonds3 = $this->md(Fonds::create( $this->even_more_valid_subfonds_data ));
        $this->assertEquals( 1, $fonds1->position);
        $this->assertEquals( 2, $fonds2->position);
        $this->assertEquals( 3, $fonds3->position);
    }

    public function test_it_throws_if_trying_to_move_to_a_non_existant_fonds()
    {
        $this->expectException( ModelNotFoundException::class );
        $fonds1 = $this->md(Fonds::create( $this->valid_subfonds_data ));

        $fonds1->moveBefore(PHP_INT_MAX);
    }

    public function test_when_moving_a_subfonds_before_the_first_the_structure_remains_intact()
    {
        $fonds1 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data ) );
        $fonds2 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data ) );
        $fonds3 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data ) );

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
        $fonds1 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data ) );
        $fonds2 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data ) );
        $fonds3 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data ) );

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
        $fonds1 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data ) );
        $fonds2 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data ) );
        $fonds3 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data ) );

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
        $fonds1 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->valid_subfonds_data ) );
        $fonds2 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->more_valid_subfonds_data ) );
        $fonds3 = $this->md( Fonds::create( ['parent_id' => $this->topLevelFonds->id] + $this->even_more_valid_subfonds_data ) );

        $fonds1->moveAfter( $fonds3->id );
        $fonds1->refresh();
        $fonds2->refresh();
        $fonds3->refresh();
        $this->assertEquals( 0, $fonds2->position );
        $this->assertEquals( 1, $fonds3->position );
        $this->assertEquals( 2, $fonds1->position );
    }


}
