<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Aip;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Aip::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Aip::class, 'dummyData', function (Faker $faker) {
    return [
        "external_uuid" => Uuid::generate()->string,
        "owner"  => Uuid::generate()->string,
        "online_storage_location_id" => 0,
        "online_storage_path" => ".",
        "offline_storage_location_id" => 0,
        "offline_storage_path" => "."
    ];
});

$factory->state(Aip::class, 'fileArchiveServiceTest', function (Faker $faker) {
    return [
        "external_uuid" => Uuid::generate()->string,
        "owner"  => Uuid::generate()->string,
        "online_storage_location_id" => 0,
        "online_storage_path" => "",
        "offline_storage_location_id" => 0,
        "offline_storage_path" => "."
    ];
});

