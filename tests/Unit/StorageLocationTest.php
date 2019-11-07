<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\StorageLocation;

class StorageLocationTest extends TestCase
{
    /**
     * Test that we get the shorthand name for the dip storable type
     *
     * @return void
     */
    public function test_given_a_storage_location_when_the_storable_type_is_dip_it_returns_dip()
    {
        $storageLocation = new StorageLocation();
        $storageLocation->storable_type = 'App\Dip';

        $this->assertEquals('dip', $storageLocation->storableType());
    }

    /**
     * Test that we get the shorthand name for the aip storable type
     *
     * @return void
     */

    public function test_given_a_storage_location_when_the_storable_type_is_aip_it_returns_aip()
    {
        $storageLocation = new StorageLocation();
        $storageLocation->storable_type = 'App\Aip';

        $this->assertEquals('aip', $storageLocation->storableType());
    }

}
