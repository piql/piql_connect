<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Fonds;

class FondsModelTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_creates_top_level_fonds_by_default()
    {
        $fonds = Fonds::create(['title' => 'top-level-fonds', 'owner_holding_uuid' => 'b0c7100d-2920-4340-a09c-e5ccee0fa90e']);
        $fonds->fresh();
        $this->assertTrue($fonds->ancestor() ==  null );
        $fonds->delete();
    }
}
