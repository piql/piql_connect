<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Job;
use App\User;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Job::class, function (Faker $faker) {
    return [
        'name' => $faker->company(),
        'uuid' => $faker->uuid(),
        'status' => 'created',
        'owner' => User::pluck('id')->random()
    ];
});
