<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bag;
use App\Job;
use App\BagJob;
use App\User;
use Faker\Generator as Faker;

$factory->define(BagJob::class, function (Faker $faker) {
    $u = User::where('username','kare')->first()->id;
    //WARNING: This will join bags and jobs at random, which is only suitable for user interface tests
    return [
       'bag_id' => Bag::doesntHave('job')->where( 'owner', $u )->where('status','complete')->pluck('id')->random(),
       'job_id' => Job::where( 'owner', $u )->where('status','created')->pluck('id')->random()
    ];
});
