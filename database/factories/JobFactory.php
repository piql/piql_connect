<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Job;
use App\User;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Job::class, function (Faker $faker) {
    return [
        'name' => $faker->company(),
        'owner' => User::pluck('id')->random()
    ];
});

$factory->state( Job::class, 'ingesting', function() {

    $owner = Auth::check()
        ? Auth::id()
        : User::pluck('id')->random();

    return [
        'owner' => $owner
    ];
});

$factory->afterCreatingState(Job::class, 'ingesting', function ($job) {
    $job->applyTransition('piql_it')->save();
});
