<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Holding;
use App\Archive;
use Faker\Generator as Faker;

$factory->define(Holding::class, function (Faker $faker) {
    if(Archive::count() == 0){
        throw new Exception("Holding factory failed: You need to create at least one Archive first");
    }
    return [
        'title' => $faker->slug(1),
        'description' => $faker->text(80),
        'owner_archive_uuid' => Archive::where('parent_uuid', '=', null)->pluck('uuid')->random(),
    ];
});

$factory->state(Holding::class, 'subholdings', function (Faker $faker) {
    if(Holding::count() == 0){
        throw new Exception("Holding factory failed: You need to create at least one holding before creating any subholdings.");
    }
    return [
        'parent_id' => Holding::pluck('id')->random()
    ];
});
