<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Metadata;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Metadata::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid,
        'modified_by' => $faker->uuid,
        'metadata' => [ "dc:title" => "The greatest story ever told" ],
    ];
});
