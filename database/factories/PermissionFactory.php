<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\PermissionType;
use App\Permission;
use Faker\Generator as Faker;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->realText(200, 2),
        'type' => PermissionType::Role,
    ];
});
