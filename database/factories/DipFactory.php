<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Dip;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Dip::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Dip::class, 'dummyData', function (Faker $faker) {
    return [
        "external_uuid" => Uuid::generate()->string,
        "owner"  => Uuid::generate()->string,
        "aip_external_uuid" => Uuid::generate()->string,
        "storage_location_id" => 0,
        "storage_path" => ".",
    ];
});
