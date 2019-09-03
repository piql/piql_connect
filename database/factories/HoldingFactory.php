<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Holding;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;


$factory->define(Holding::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid(),
        'title' => $faker->company(),
        'description' => $faker->text(80),
        'parent_uuid' => null
    ];
});

/* Sub-holdings belong to a parent, but only one level of nesting should be allowed - it is not a tree */
$factory->state(Holding::class, 'subholding', function (Faker $faker) {
    return [
        'parent_uuid' => Holding::count() > 0 ? Holding::where('parent_uuid', '=', null)->pluck('uuid')->random() : null
    ];
});
