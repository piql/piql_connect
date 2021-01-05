<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Holding;
use App\Collection;
use Faker\Generator as Faker;

$factory->define(Holding::class, function (Faker $faker) {
    if(Collection::count() == 0){
        throw new Exception("Holding factory failed: You need to create at least one Collection first");
    }
    return [
        'title' => $faker->slug(1),
        'description' => $faker->text(80),
        'collection_uuid' => Collection::pluck('uuid')->random(),
    ];
});
