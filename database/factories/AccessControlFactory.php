<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\AccessControlType;
use App\AccessControl;
use Faker\Generator as Faker;

$factory->define(AccessControl::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->realText(200, 2),
        'type' => AccessControlType::Role,
    ];
});
