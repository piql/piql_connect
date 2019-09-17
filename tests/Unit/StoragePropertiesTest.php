<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\StorageProperties;
use App\Bag;
use App\User;

class StoragePropertiesTest extends TestCase
{
    public function test_when_creating_a_bag_it_creates_a_storage_properties_for_it()
    {
        $bag = Bag::create(['name' => 'TestStorageProperties', 'owner' => User::first()->id]);
        $storage_properties = $bag->storage_properties->get();
        $this->assertNotNull($storage_properties);
        $bag->delete();
    }

    public function test_when_deleting_a_bag_it_cascades_to_the_bags_storage_properties()
    {
        $bag = Bag::create(['name' => 'TestStorageProperties', 'owner' => User::first()->id]);
        $storage_properties_id = $bag->storage_properties->id;
        $bag->delete();
        $this->assertNull(StorageProperties::find($storage_properties_id));
    }
}
