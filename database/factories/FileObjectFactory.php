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
        "object_type" => "file",
        "info_source" => "connect",
        "mime_type" => "text/plain",
        "storable_type" => $faker->randomElement(['App\Aip', 'App\Dip']),
        "storable_id" => 0,
    ];
});

$factory->state(FileObject::class, 'fileArchiveServiceTest', function (Faker $faker) {
    $filename = $faker->slug(2).$faker->randomElement(['.tiff', '.pdf','.doc','.jpeg']);
    return [
        "fullpath" => $faker->uuid()."/".$filename,
        "filename" => $filename,
        "path" => ".",
        "size" => $faker->biasedNumberBetween(1, 10000000),
        "object_type" => 'file',
        "info_source" => "",
        "mime_type" => "text/plain",
        "storable_type" => "App\Aip",
        "storable_id" => 0,
    ];
});
