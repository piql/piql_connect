<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use App\Bag;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Bag::class, function (Faker $faker) {

    if( User::count() == 0 ) {
        throw new Exception( 'Bag factory failed: Cannot create Bags without users. Seed one or more users first. ');
    }
    $owner = User::pluck('id')->random();

    $chance = rand(0,1);

    $year = date("Y");

    if ($chance == 0)
    {
        $year--;
    }

    $today = date('Y-n-j H:i:s');

    do
    {
        $dateTime = $year . "-" . $faker->month . "-" . $faker->dayOfMonth . " " . $faker->time;
    } while (strtotime($dateTime) > strtotime($today));
    $name = str_replace([" ", "-", ":"], "", $dateTime);

    $chance = rand(0,9);

    if ($chance == 0)
    {
        $status = "created";
    } elseif ($chance == 1)
    {
        $status = "processing";
    } else
    {
        $status = "complete";
    }

    return [
        'name' => $name,
        'status' => $status,
        'owner' => $owner,
        'created_at' => $dateTime
    ];
});
