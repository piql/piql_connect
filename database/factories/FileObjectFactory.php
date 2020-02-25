<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FileObject;
use Faker\Generator as Faker;

$factory->define(FileObject::class, function (Faker $faker) {
    return [
        //
    ];
});


$factory->state(FileObject::class, 'dummyData', function (Faker $faker) {

    return [
        "fullpath" => "./". $faker->firstName,
        "filename" => $faker->firstName,
        "path" => ".",
        "size" => $faker->biasedNumberBetween(1, 10000000),
        "object_type" => $faker->randomElement(['App\Aip', 'App\Dip']),
        "info_source" => "",
        "mime_type" => "text/plain",
        "storable_type" => "",
        "storable_id" => 0,
    ];
});
