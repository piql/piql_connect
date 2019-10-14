<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Archive;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;


$factory->define(Archive::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid(),
        'title' => $faker->company(),
        'description' => $faker->text(80),
        'parent_uuid' => null
    ];
});

/* Sub-holdings belong to a parent, but only one level of nesting should be allowed - it is not a tree */
$factory->state(Archive::class, 'subholding', function (Faker $faker) {
    return [
        'parent_uuid' => Archive::count() > 0 ? Archive::where('parent_uuid', '=', null)->pluck('uuid')->random() : null
    ];
});
