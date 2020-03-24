<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\StorageLocation;

$factory->define(StorageLocation::class, function (Faker $faker) {
    return [
        //
    ];
});


$factory->state(StorageLocation::class, 'fileArchiveServiceTest', function (Faker $faker) {
    return [
        "locatable_type" => 'fakestorage',
        "locatable_id" => 0,
        "owner_id" => App\User::first(),
        "storable_type" => 'App\Aip',
        'human_readable_name' => 'Fake s3 for testing',
    ];
});
