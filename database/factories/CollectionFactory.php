<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Collection;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;


$factory->define(Collection::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid(),
        'title' => $faker->company(),
        'description' => $faker->text(80),
        'defaultMetadataTemplate' => ["dc" => ["relation" => $faker->text(8) ]]
    ];
});
