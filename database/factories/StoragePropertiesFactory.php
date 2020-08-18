<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StorageProperties;
use Faker\Generator as Faker;

$factory->define(StorageProperties::class, function (Faker $faker) {
    return [
        'bag_uuid' => $faker->uuid(),
        'aip_initial_online_storage_location' => $faker->numberBetween(0,10),
        'dip_initial_storage_location' => $faker->numberBetween(0,10),
        'name' => $faker->slug(3)
    ];
});
