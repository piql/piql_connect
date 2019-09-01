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
        $fonds = Fonds::create();
        $fonds->fresh();
        $this->assertTrue($fonds->ancestor() ==  null );
    }
}
