<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Job;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Job::class, function (Faker $faker) {
    $uuid = Uuid::generate();
    return [
        'name' => 'job-'.$uuid,
        'uuid' => $uuid,
        'status' => 'created',
        'owner' => '',
    ];
});
