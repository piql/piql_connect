<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Organization;
use Faker\Generator as Faker;

$factory->define(Organization::class, function (Faker $faker) {
    return [
        "name" => $faker->company." ".$faker->companySuffix,
        "uuid" => $faker->uuid,
    ];
});
